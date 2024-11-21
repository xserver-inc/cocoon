import { THEME_NAME, hexToRgba, arrayValueTotal, getChartJsFontHeight } from '../../helpers';
import { useBlockProps } from '@wordpress/block-editor';
import classnames from 'classnames';
const DEFAULT_COLOR = 'rgba(255, 99, 132, 0.2)';
const DEFAULT_BORDER_COLOR = 'rgba(255, 99, 132, 0.9)';
import { __ } from '@wordpress/i18n';

export default function save( props ) {
  const { attributes } = props;
  const {
    chartId,
    chartColor,
    backgroundColor,
    fontColor,
    gridColor,
    canvasSize,
    fontSize,
    fontWeight,
    maximum,
    displayTitle,
    title,
    displayLegend,
    legendText,
    labels,
    data,
    displayTotal,
    displayLabelValue,
    displayAngleLines,
  } = attributes;

  const className = classnames( 'block-box', 'wp-block', 'radar-chart-block');

  const blockProps = useBlockProps.save( {
    className: className,
  } );

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
            label: '${legendText}',
            data: ${JSON.stringify(data)},
            backgroundColor: '${chartColor ? hexToRgba(chartColor, 0.2) : DEFAULT_COLOR}',
            borderColor: '${chartColor ? hexToRgba(chartColor, 0.9) : DEFAULT_BORDER_COLOR}',
            borderWidth: 1,
          }]
        },
        options: {
          layout: {
            padding: {
              top: 30,    // 上の余白
              bottom: 30, // 下の余白
              left: 30,   // 左の余白
              right: 30,  // 右の余白
            },
          },
          scales: {
            r: {
              angleLines: {
                display: ${displayAngleLines},
                color: '${hexToRgba(gridColor, 0.5)}', // アングルライン線の色
              },
              min: 0,
              max: ${maximum},
              ticks: {
                stepSize: ${(maximum === 100) ? 10 : 1},
                font: {
                  size: ${fontSize},
                  weight: ${fontWeight},
                },
                color: '${fontColor}',
                backdropColor: 'transparent', // 目盛の背景色を透明に
              },
              pointLabels: {
                font: {
                  size: ${fontSize},
                  weight: ${fontWeight},
                },
                color: '${fontColor}',
              },
              grid: {
                color: '${hexToRgba(gridColor, 0.5)}', // グリッド線の色
              }
            },
          },
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            title: {
              display: ${displayTitle},
              text: '${title}',
              font: {
                size: ${fontSize + 2},
                weight: ${fontWeight + 100},
              },
              color: '${fontColor}',
            },
            legend: {
              display: ${displayLegend},
              labels: {
                font: {
                  size: ${fontSize},
                  weight: ${fontWeight},
                },
                color: '${fontColor}',
              },
            },
            totalPlugin: true // 合計表示プラグインを有効化
          },
        },
        plugins: [
          {
            id: 'totalPlugin',
            afterDatasetsDraw(chart, args, options) {
              if (${displayTotal}) {
                const { ctx, chartArea: { top, left, right, bottom } } = chart;
                const centerX = (left + right) / 2;
                const centerY = (top + bottom + ${((data.length % 2 === 0) ? 0 : getChartJsFontHeight(fontSize))}) / 2;
                const total = '${__('総計:', THEME_NAME)} ${arrayValueTotal(data)}';

                ctx.save();
                ctx.font = '${fontSize}px';
                ctx.fillStyle = '${fontColor}';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(total, centerX, centerY);
                ctx.restore();
              }
            }
          },
          {
            id: 'customCanvasBackgroundColor',
            beforeDraw: (chart) => {
              const ctx = chart.ctx;
              const canvas = chart.canvas;
              ctx.save();
              ctx.globalCompositeOperation = 'destination-over'; // 背景色を他の要素の背後に描画
              ctx.fillStyle = '${backgroundColor}'; // キャンバスの背景色を設定
              ctx.fillRect(0, 0, canvas.width, canvas.height); // キャンバス全体に背景色を塗る
              ctx.restore();
            }
          }
        ]
      });
    };

    renderChart();

    window.addEventListener('resize', renderChart);
  });
  `;

  return (
    <div { ...blockProps }>
      <canvas id={chartId} width={canvasSize} height={canvasSize} />
      <script>{chartScript}</script>
    </div>
  );
}
