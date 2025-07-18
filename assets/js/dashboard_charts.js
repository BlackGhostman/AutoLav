// assets/js/dashboard_charts.js

document.addEventListener('DOMContentLoaded', function () {
    // Gráfico para el Dashboard de Administrador
    const adminChartCanvas = document.getElementById('adminChart');
    if (adminChartCanvas) {
        fetch('api_dashboard.php?chart=admin_weekly_performance')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error fetching admin chart data:', data.error);
                    return;
                }

                new Chart(adminChartCanvas, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: data.datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            'y-ingresos': {
                                type: 'linear',
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Ingresos (₡)'
                                }
                            },
                            'y-servicios': {
                                type: 'linear',
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'N° de Servicios'
                                },
                                grid: {
                                    drawOnChartArea: false
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Fetch error:', error));
    }

    // Gráfico para el Dashboard de Caja
    const cajaChartCanvas = document.getElementById('cajaChart');
    if (cajaChartCanvas) {
        fetch('api_dashboard.php?chart=caja_flow')
            .then(response => response.json())
            .then(data => {
                if (data.error) return console.error(data.error);
                new Chart(cajaChartCanvas, {
                    type: 'bar',
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    },
                    data: data
                });
            });
    }

    // Gráfico para el Dashboard de Lavado
    const lavadoChartCanvas = document.getElementById('lavadoChart');
    if (lavadoChartCanvas) {
        fetch('api_dashboard.php?chart=lavado_productivity')
            .then(response => response.json())
            .then(data => {
                if (data.error) return console.error(data.error);
                new Chart(lavadoChartCanvas, { type: 'doughnut',
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }, data: data });
            });
    }
});
