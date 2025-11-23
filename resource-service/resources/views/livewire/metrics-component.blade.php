<div wire:poll.2s="fetchMetrics" style="width: 100%; max-width: 1200px; margin: auto;">
    <h2 class="text-2xl font-bold mb-4">System Metrics (Realtime)</h2>

    <canvas id="metricsChart" height="120"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('livewire:init', () => {

    let chart = null;

    Livewire.on('updateChart', (data) => {

        if (chart === null) {

            const ctx = document.getElementById('metricsChart').getContext('2d');

            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: "CPU (%)",
                            borderColor: "red",
                            data: data.cpu,
                        },
                        {
                            label: "Memory (%)",
                            borderColor: "blue",
                            data: data.memory,
                        },
                        {
                            label: "Disk (%)",
                            borderColor: "orange",
                            data: data.disk,
                        }
                    ]
                },
                options: { animation: false, responsive: true }
            });

        } else {

            chart.data.labels = data.labels;
            chart.data.datasets[0].data = data.cpu;
            chart.data.datasets[1].data = data.memory;
            chart.data.datasets[2].data = data.disk;
            chart.update();

        }
    });
});
</script>
