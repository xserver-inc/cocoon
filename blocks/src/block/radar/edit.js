import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { useEffect, useRef } from '@wordpress/element';

export default function edit( props ) {
  const { attributes, setAttributes } = props;
  const { labels, data, chartId } = attributes;
  const canvasRef = useRef(null);
  let chartInstance = null;

  useEffect(() => {
    if (!chartId) {
      const newChartId = `radarChart-${Math.random().toString(36).substr(2, 9)}`;
      setAttributes({ chartId: newChartId });
    }
  }, []);

  const renderChart = () => {
    const ctx = canvasRef.current.getContext('2d');

    if (chartInstance) {
      chartInstance.destroy();
    }

    chartInstance = new Chart(ctx, {
      type: 'radar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Sample Data',
          data: data,
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

  useEffect(() => {
      if (!chartId) return;

      renderChart();

      const handleResize = () => {
          renderChart();
      };

      window.addEventListener('resize', handleResize);

      return () => {
        window.removeEventListener('resize', handleResize);
        if (chartInstance) {
          chartInstance.destroy();
        }
      };
  }, [labels, data, chartId]);

  return (
      <div className="my-radar-chart-block">
          <canvas ref={canvasRef} id={chartId} width="400" height="400" />
          <InspectorControls>
              <PanelBody title="Chart Settings">
                  <TextControl
                      label="Labels (comma separated)"
                      value={labels.join(', ')}
                      onChange={(value) => setAttributes({ labels: value.split(',').map(label => label.trim()) })}
                  />
                  <TextControl
                      label="Data (comma separated, 0-5)"
                      value={data.join(', ')}
                      onChange={(value) => {
                          const newData = value.split(',').map(d => {
                              const num = parseFloat(d.trim());
                              return Math.max(0, Math.min(5, num));
                          });
                          setAttributes({ data: newData });
                      }}
                  />
              </PanelBody>
          </InspectorControls>
      </div>
  );
}
