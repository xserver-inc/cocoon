import { useRef, useEffect } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { store as blockEditorStore } from '@wordpress/block-editor';
import { compose } from '@wordpress/compose';
import { InspectorControls, withColors } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export function edit( props ) {
  const { attributes, setAttributes, clientId } = props;
  const { labels, data, chartId } = attributes;
  const canvasRef = useRef(null);

  let chartInstance = null;

  const { isSelected } = useSelect((select) => ({
  isSelected: select(blockEditorStore).isBlockSelected(clientId),
  }));

  const { selectBlock } = useDispatch(blockEditorStore);

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
      // label: 'Sample Data',
      data: data,
      backgroundColor: 'rgba(255, 99, 132, 0.2)',
      borderColor: 'rgba(255, 99, 132, 1)',
      borderWidth: 1
    }]
    },
    options: {
    scales: {
      r: {
      min: 0,
      max: 5,
      ticks: {
        stepSize: 1,
      },
      }
    },
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
      display: false
      },
    },
    },
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

  // Add click event to canvas to focus/select block
  useEffect(() => {
  if (canvasRef.current) {
    const handleClick = () => {
    selectBlock(clientId);
    };
    canvasRef.current.addEventListener('click', handleClick);

    return () => {
    canvasRef.current.removeEventListener('click', handleClick);
    };
  }
  }, [canvasRef.current]);

  return (
    <div className="radar-chart-block">
      <canvas ref={canvasRef} id={chartId} width="400" height="400" tabIndex="0" />
      <InspectorControls>
        <PanelBody title={ __( 'チャート設定', 'THEME_NAME' ) }>
          <TextControl
            label={ __( '項目', 'THEME_NAME' ) + __( '（カンマ区切り）', 'THEME_NAME' ) }
            value={labels.join(', ')}
            onChange={(value) => setAttributes({ labels: value.split(',').map(label => label.trim()) })}
          />
          <TextControl
            label={ __( '値', 'THEME_NAME' ) + __( '（カンマ区切り）', 'THEME_NAME' ) }
            value={data.join(', ')}
            onChange={(value) => {
              const newData = value.split(',').map(d => {
                let num = parseFloat(d.trim());
                num = Math.max(0, Math.min(5, num));
                if (isNaN(num)) {
                num = '';
                }
                return num;
              });
              setAttributes({ data: newData });
            }}
          />
        </PanelBody>
      </InspectorControls>
    </div>
  );
}


export default compose( [
  withColors( 'backgroundColor', {
  textColor: 'color',
  } ),
] )( edit );