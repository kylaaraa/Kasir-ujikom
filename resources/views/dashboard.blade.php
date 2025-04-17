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
            <!-- Chart Atas -->
            <div class="bg-white p-6 rounded-xl shadow border md:w-1/2">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Persentase Penjualan Produk</h3>
                <div class="w-64 h-64 mx-auto">
                    <canvas id="overallPieChart"></canvas>
                </div>
            </div>

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

            {{-- chart bawah --}}
        <div class="flex flex-col md:flex-row gap-6 mt-4">
            <!-- Pie Chart: Stok Produk -->
            <div class="bg-white p-6 rounded-xl shadow border md:w-1/2">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Stok Barang</h3>
                <div class="w-64 h-64 mx-auto">
                    <canvas id="overallPieChart2"></canvas>
                </div>
            </div>

            <!-- Bar Chart: Jumlah Transaksi Member & Non-Member -->
            <div class="bg-white p-6 rounded-xl shadow border md:w-1/2">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Transaksi Member vs Non-Member</h3>
                <div class="w-full">
                    <canvas id="salesChart2" height="300"></canvas>
                </div>
            </div>
        </div>


    @endif
</div>
@endsection

{{-- Hanya jalankan script chart kalau role admin --}}
@push('scripts')
@if ($user->role === 'admin')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const produkLabels = @json(array_keys($totalPerProduk));
    const produkTotals = @json(array_values($totalPerProduk));
    const pieColors = [
        '#f87171', '#60a5fa', '#fbbf24', '#34d399', '#c084fc',
        '#f97316', '#ec4899', '#22d3ee', '#818cf8', '#fde68a',
        '#86efac', '#fca5a5'
    ];

    function createPieChart(canvasId, labels, data) {
        const ctx = document.getElementById(canvasId)?.getContext('2d');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: pieColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
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
    }

    function createBarChart(canvasId, rawData, labelText = 'Total Penjualan') {
        const ctx = document.getElementById(canvasId)?.getContext('2d');
        if (!ctx || rawData.length === 0) return;
        const totalPenjualan = rawData.map(item => item.total);
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [labelText],
                datasets: [{
                    label: labelText,
                    data: totalPenjualan,
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false },
                },
                scales: {
                    x: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: 0,
                            minRotation: 0
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

    // Chart atas
    const chartData = @json($chartData);
    createPieChart('overallPieChart', produkLabels, produkTotals);
    createBarChart('salesChart', chartData);

    // Chart bawah: stok produk
    const stokLabels = @json(array_keys($stokPerProduk));
    const stokData = @json(array_values($stokPerProduk));
    createPieChart('overallPieChart2', stokLabels, stokData);

    // Chart bawah: member vs non-member
    const memberLabels = @json(array_keys($memberStats));
    const memberData = @json(array_values($memberStats));
    const ctxBar2 = document.getElementById('salesChart2')?.getContext('2d');
    if (ctxBar2) {
        new Chart(ctxBar2, {
            type: 'bar',
            data: {
                labels: memberLabels,
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: memberData,
                    backgroundColor: ['rgba(16, 185, 129, 0.6)', 'rgba(239, 68, 68, 0.6)'],
                    borderColor: ['rgba(16, 185, 129, 1)', 'rgba(239, 68, 68, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }
});
</script>
@endif
@endpush

