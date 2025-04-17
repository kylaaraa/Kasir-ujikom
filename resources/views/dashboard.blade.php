@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="bg-white p-6 rounded-xl shadow border mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">
            Selamat Datang, {{ ucfirst($user->role) }}!
        </h2>
    </div>

    @if ($user->role === 'petugas')
        <div class="bg-white p-6 rounded-xl shadow border">
            <div class="bg-gray-100 rounded-lg overflow-hidden">
                <div class="text-center bg-gray-100 py-3 font-semibold text-gray-600">
                    Total Penjualan Hari Ini
                </div>
                <div class="py-8 text-center">
                    <p class="text-4xl font-bold text-gray-800">{{ $totalPenjualan }}</p>
                    <p class="mt-2 text-sm text-gray-500">Jumlah total penjualan yang terjadi hari ini.</p>
                </div>
                <div class="text-center text-xs text-gray-400 bg-gray-100 py-2">
                    Terakhir diperbarui: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}
                </div>
            </div>
        </div>

    @elseif ($user->role === 'admin')
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Persentase Penjualan Semua Produk -->
            <div class="bg-white p-6 rounded-xl shadow border md:w-1/2">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Persentase Penjualan Produk</h3>
                <div class="w-64 h-64 mx-auto">
                    <canvas id="overallPieChart"></canvas>
                </div>
            </div>

            <!-- Grafik Penjualan -->
            <div class="bg-white p-6 rounded-xl shadow border md:w-1/2 overflow-x-auto">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Grafik Penjualan Produk</h3>
                <div class="w-[1000px]">
                    <canvas id="salesChart" height="300"></canvas>
                </div>

                @if(empty($chartData))
                    <p class="text-sm text-gray-500 mt-2">Belum ada data untuk ditampilkan dalam grafik.</p>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Pie Chart Data: Gabungan Penjualan Semua Hari
    const produkLabels = @json(array_keys($totalPerProduk));
    const produkTotals = @json(array_values($totalPerProduk));

    const pieColors = [
        '#f87171', '#60a5fa', '#fbbf24', '#34d399', '#c084fc',
        '#f97316', '#ec4899', '#22d3ee', '#818cf8', '#fde68a',
        '#86efac', '#fca5a5'
    ];

    const ctxPie = document.getElementById('overallPieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: produkLabels,
            datasets: [{
                data: produkTotals,
                backgroundColor: pieColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(2) + '%';
                            return `${label}: ${value} (${percentage})`;
                        }
                    }
                }
            }
        }
    });

    // Bar Chart Data
    const rawData = @json($chartData);

    if (rawData.length > 0) {
        const labels = rawData.map(item => item.tanggal);
        const totalPenjualan = rawData.map(item => {
            const sum = Object.entries(item)
                .filter(([key]) => key !== 'tanggal')
                .reduce((acc, [_, val]) => acc + (val ?? 0), 0);
            return sum;
        });

        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Penjualan',
                    data: totalPenjualan,
                    backgroundColor: 'rgba(59, 130, 246, 0.3)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { mode: 'index', intersect: false },
                },
                scales: {
                    x: {
                        ticks: {
                            maxRotation: 60,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }
</script>
@endpush