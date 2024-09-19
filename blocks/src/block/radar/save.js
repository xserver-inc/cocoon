export default function save( props ) {
  const { attributes } = props;
  const { labels, data, chartId } = attributes;

  const chartScript = `
    document.addEventListener('DOMContentLoaded', function() {
      var ctx = document.getElementById('${chartId}').getContext('2d');

      var renderChart = function() {
        if (ctx.chart) {
            ctx.chart.destroy();
        }

        ctx.chart = new Chart(ctx, {
          type: 'radar',
          data: {
            labels: ${JSON.stringify(labels)},
            datasets: [{
              label: 'Sample Data',
              data: ${JSON.stringify(data)},
              backgroundColor: 'rgba(255, 99, 132, 0.2)',
              borderColor: 'rgba(255, 99, 132, 1)',
              borderWidth: 1
            }]
          },
          options: {
              scales: {
                r: {
                  angleLines: {
                    display: false
                  },
                  min: 0,
                  max: 5,
                  ticks: {
                    stepSize: 1
                  }
                }
              },
              responsive: true,
              maintainAspectRatio: false,
            }
          });
        };

        renderChart();

        window.addEventListener('resize', renderChart);
    });
  `;

  return (
    <div className="my-radar-chart-block">
      <canvas id={chartId} width="400" height="400" />
      <script>{chartScript}</script>
    </div>
  );
}
