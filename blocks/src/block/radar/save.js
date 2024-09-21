import { hexToRgba,  radarValueTotal } from '../../helpers';
const DEFAULT_COLOR = 'rgba(255, 99, 132, 0.2)';
const DEFAULT_BORDER_COLOR = 'rgba(255, 99, 132, 0.9)';

export default function save( props ) {
  const { attributes } = props;
  const { canvasSize, maximum, displayLegend, legendText, displayTotal, labels, data, chartId, displayAngleLines, displayLabelValue, pointLabelFontSize, chartColor } = attributes;

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
        labels: ${JSON.stringify(labels.map((label, index) => displayLabelValue ? `${label} ( ${data[index]} )` : label))},
        datasets: [{
          label: '${displayTotal ? legendText + radarValueTotal(data) : legendText}',
          data: ${JSON.stringify(data)},
          backgroundColor: '${chartColor ? hexToRgba(chartColor, 0.2) : DEFAULT_COLOR}',
          borderColor: '${chartColor ? hexToRgba(chartColor, 0.9) : DEFAULT_BORDER_COLOR}',
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          r: {
            angleLines: {
              display: ${displayAngleLines}
            },
            min: 0,
            max: ${maximum},
            ticks: {
              stepSize: ${(maximum === 100) ? 10 : 1}
            },
            pointLabels: {
              font: {
                size: ${pointLabelFontSize}
              }
            }
          }
        },
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: ${displayLegend}
          }
        }
      }
      });
    };

    renderChart();

    window.addEventListener('resize', renderChart);
  });
  `;

  return (
  <div className="radar-chart-block">
    <canvas id={chartId} width={canvasSize} height={canvasSize} />
    <script>{chartScript}</script>
  </div>
  );
}
