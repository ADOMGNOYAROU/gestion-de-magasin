import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    const dataEl = document.getElementById('dashboard-chart-data');
    if (!dataEl) return;

    const { ventesParJour = [], ventesParProduit = [] } = JSON.parse(dataEl.textContent);

    const jourCanvas = document.getElementById('ventesParJourChart');
    if (jourCanvas) {
        new Chart(jourCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: ventesParJour.map((item) => item.date),
                datasets: [
                    {
                        label: "Chiffre d'affaires (FCFA)",
                        data: ventesParJour.map((item) => item.ca),
                        borderColor: 'rgb(22, 101, 52)',
                        backgroundColor: 'rgba(22, 101, 52, 0.15)',
                        tension: 0.1,
                        fill: true,
                    },
                    {
                        label: 'Nombre de ventes',
                        data: ventesParJour.map((item) => item.ventes),
                        borderColor: 'rgb(202, 138, 4)',
                        backgroundColor: 'rgba(202, 138, 4, 0.15)',
                        tension: 0.1,
                        fill: true,
                        yAxisID: 'y1',
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'CA (FCFA)' },
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'Ventes' },
                        grid: { drawOnChartArea: false },
                    },
                },
            },
        });
    }

    const produitCanvas = document.getElementById('ventesParProduitChart');
    if (produitCanvas) {
        new Chart(produitCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ventesParProduit.map((item) => item.nom),
                datasets: [
                    {
                        label: 'Quantité vendue',
                        data: ventesParProduit.map((item) => item.quantite),
                        backgroundColor: 'rgba(22, 101, 52, 0.8)',
                        borderColor: 'rgba(22, 101, 52, 1)',
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Quantité' },
                    },
                },
            },
        });
    }
});
