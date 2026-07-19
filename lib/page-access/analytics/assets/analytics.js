/**
 * Cocoon Analytics Dashboard - Chart rendering
 * 依存: Chart.js (UMD), CocoonAnalytics (wp_localize_script)
 */
(function () {
  'use strict';

  if (typeof window.Chart === 'undefined' || typeof window.CocoonAnalytics === 'undefined') {
    return;
  }

  const CA = window.CocoonAnalytics;
  const data = CA.data || {};
  const i18n = CA.i18n || {};

  // 共通カラーパレット
  const palette = [
    '#4285F4', '#EA4335', '#FBBC04', '#34A853', '#673AB7',
    '#00ACC1', '#FF7043', '#AB47BC', '#26A69A', '#5C6BC0'
  ];

  // ドーナツ/パイの共通オプション（凡例を右に表示してグラフを潰さない）
  // 狭い画面では右側に凡例を置くとラベルがはみ出すため、下配置に切り替える
  const doughnutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: window.matchMedia('(max-width: 600px)').matches ? 'bottom' : 'right',
        labels: {
          boxWidth: 14,
          font: { size: 11 },
          padding: 8
        }
      }
    }
  };

  // カスタム期間の表示切替
  const periodSelect = document.querySelector('.cocoon-analytics-period-form select[name="period"]');
  if (periodSelect) {
    periodSelect.addEventListener('change', () => {
      const range = document.querySelector('.cocoon-analytics-period-form .cocoon-analytics-custom-range');
      if (range) {
        range.style.display = periodSelect.value === 'custom' ? '' : 'none';
      }
    });
  }

  // PV数推移（日/週/月の切り替え機能付き）
  (function () {
    const canvas = document.getElementById('cocoon-analytics-trend');
    if (!canvas) {return;}

    // トグルボタン群を取得します
    const buttons = document.querySelectorAll('.cocoon-analytics-trend-btn');
    if (!buttons || !buttons.length) {return;}

    let chartInstance = null;

    // 選択されたタイプ（daily / weekly / monthly）に応じてデータを取得し、グラフを構築またはアップデートします
    function updateTrendChart(type) {
      // データの存在チェックを行います
      const list = data[type];
      if (!list || !list.length) {
        if (chartInstance) {
          chartInstance.destroy();
          chartInstance = null;
        }
        canvas.style.display = 'none';
        return;
      }
      canvas.style.display = 'block';

      // Chart.jsに渡すデータ形式を構築します
      const labels = list.map((d) => { return d.date; });
      const pvData = list.map((d) => { return d.pv; });

      const config = {
        type: type === 'daily' ? 'line' : 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: i18n.pv || 'PV',
            data: pvData,
            fill: type === 'daily',
            tension: 0.25
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } }
          }
        }
      };

      // グラフの種類に応じた見た目の微調整を設定します
      if (type === 'daily') {
        config.data.datasets[0].borderColor = palette[0];
        config.data.datasets[0].backgroundColor = 'rgba(66, 133, 244, 0.15)';
        config.data.datasets[0].pointRadius = list.length > 60 ? 0 : 3;
      } else if (type === 'weekly') {
        config.data.datasets[0].backgroundColor = 'rgba(52, 168, 83, 0.7)';
        config.data.datasets[0].borderColor = '#34A853';
        config.options.plugins.tooltip = {
          callbacks: {
            afterLabel: function (ctx) {
              const row = data.weekly[ctx.dataIndex];
              if (row && row.days && row.days < 7) {
                return (i18n.partial_week || '部分週') + ': ' + row.days + '/7 ' + (i18n.days || '日');
              }
              return '';
            }
          }
        };
      } else if (type === 'monthly') {
        config.data.datasets[0].backgroundColor = 'rgba(251, 188, 4, 0.7)';
        config.data.datasets[0].borderColor = '#FBBC04';
        config.options.plugins.tooltip = {
          callbacks: {
            afterLabel: function (ctx) {
              const row = data.monthly[ctx.dataIndex];
              if (row && row.days) {
                return (i18n.days_count || '日数') + ': ' + row.days + ' ' + (i18n.days || '日');
              }
              return '';
            }
          }
        };
      }

      // すでにグラフが描画されている場合は一度破棄して再生成します（Chart.jsでグラフタイプ line ⇔ bar の変更を伴うため）
      if (chartInstance) {
        chartInstance.destroy();
      }
      chartInstance = new Chart(canvas, config);
    }

    // トグルボタンのクリックイベントを設定します
    buttons.forEach((btn) => {
      btn.addEventListener('click', () => {
        // アクティブ状態の見た目を切り替えます
        buttons.forEach((b) => { b.classList.remove('is-active'); });
        btn.classList.add('is-active');

        // 選択された種類のグラフを描画します
        const type = btn.getAttribute('data-type');
        updateTrendChart(type);
      });
    });

    // 初期状態として、データが存在する最初のタイプ（デフォルトは日次）を読み込みます
    let defaultType = 'daily';
    if (!data.daily || !data.daily.length) {
      if (data.weekly && data.weekly.length) {defaultType = 'weekly';}
      else if (data.monthly && data.monthly.length) {defaultType = 'monthly';}
    }

    // 初期タイプに適合するトグルボタンに is-active を付与します
    const activeBtn = document.querySelector('.cocoon-analytics-trend-btn[data-type="' + defaultType + '"]');
    if (activeBtn) {
      activeBtn.classList.add('is-active');
    }
    updateTrendChart(defaultType);
  })();

  // 投稿タイプ別
  (function () {
    const canvas = document.getElementById('cocoon-analytics-type');
    if (!canvas || !data.by_type || !data.by_type.length) {return;}
    new Chart(canvas, {
      type: 'doughnut',
      data: {
        labels: data.by_type.map((d) => { return d.post_type; }),
        datasets: [{
          data: data.by_type.map((d) => { return d.pv; }),
          backgroundColor: palette
        }]
      },
      options: doughnutOptions
    });
  })();

  // カテゴリ別（ダッシュボード用: 上位10 + その他）
  (function () {
    const canvas = document.getElementById('cocoon-analytics-category');
    if (!canvas || !data.by_category || !data.by_category.length) {return;}
    new Chart(canvas, {
      type: 'doughnut',
      data: {
        labels: data.by_category.map((d) => { return d.name; }),
        datasets: [{
          data: data.by_category.map((d) => { return d.pv; }),
          backgroundColor: palette
        }]
      },
      options: doughnutOptions
    });
  })();

  // タグ別（ダッシュボード用: 上位10 + その他）
  (function () {
    const canvas = document.getElementById('cocoon-analytics-tag');
    if (!canvas || !data.by_tag || !data.by_tag.length) {return;}
    new Chart(canvas, {
      type: 'doughnut',
      data: {
        labels: data.by_tag.map((d) => { return d.name; }),
        datasets: [{
          data: data.by_tag.map((d) => { return d.pv; }),
          backgroundColor: palette
        }]
      },
      options: doughnutOptions
    });
  })();

  // 曜日別
  (function () {
    const canvas = document.getElementById('cocoon-analytics-dow');
    if (!canvas || !data.by_dow || !data.by_dow.length) {return;}
    const weekdays = CA.weekdays || ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    new Chart(canvas, {
      type: 'bar',
      data: {
        labels: data.by_dow.map((d) => { return weekdays[d.dow - 1]; }),
        datasets: [{
          label: i18n.pv || 'PV',
          data: data.by_dow.map((d) => { return d.pv; }),
          backgroundColor: palette[3]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
      }
    });
  })();

  // タクソノミー別（上位10）
  (function () {
    const canvas = document.getElementById('cocoon-analytics-terms');
    if (!canvas || !data.terms || !data.terms.length) {return;}
    new Chart(canvas, {
      type: 'pie',
      data: {
        labels: data.terms.map((d) => { return d.name; }),
        datasets: [{
          data: data.terms.map((d) => { return d.pv; }),
          backgroundColor: palette
        }]
      },
      options: { responsive: true, maintainAspectRatio: false }
    });
  })();

  // 著者別（上位10）
  (function () {
    const canvas = document.getElementById('cocoon-analytics-authors');
    if (!canvas || !data.authors || !data.authors.length) {return;}
    new Chart(canvas, {
      type: 'bar',
      data: {
        labels: data.authors.map((d) => { return d.name; }),
        datasets: [{
          label: i18n.pv || 'PV',
          data: data.authors.map((d) => { return d.pv; }),
          backgroundColor: palette[4]
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
      }
    });
  })();

  // ==================================================================
  // ライフサイクル（2カラム、検索、無限スクロール、非同期描画）
  // ==================================================================
  (function () {
    // ライフサイクル画面のメインコンテナを探し、存在しなければ処理を中断します
    const container = document.querySelector('.cocoon-analytics-lifecycle-container');
    if (!container) {return;}

    // URLから渡された初期の投稿IDを取得します（初期値は0）
    let initialPostId = parseInt(container.getAttribute('data-initial-post-id'), 10) || 0;
    let lifecycleChart = null;

    // ライフサイクルデータのフィルタリング管理用の変数を定義します
    let rawLifecycleData = []; // サーバーから取得した全期間のアクセス生データ
    let currentPostDate = '';  // 選択された記事の公開日（カスタム期間の初期値に使用）
    let listPeriod = '30days'; // 左リストの集計期間（セレクトと同期）
    let graphPeriodOverride = null; // グラフだけ別期間を表示中のときの期間値（null=リストに追従）
    let currentSelectedPostId = initialPostId; // 現在グラフに表示している記事のID

    // HTML上の各DOM要素を取得します
    const searchInput = document.getElementById('lifecycle-search-input');
    const postsList = document.getElementById('lifecycle-posts-list');
    const listLoader = document.getElementById('lifecycle-list-loader');
    const clearBtn = document.getElementById('clear-search-btn');
    const chartTitle = document.getElementById('lifecycle-chart-title');
    const chartPlaceholder = document.getElementById('lifecycle-chart-placeholder');
    const chartWrapper = document.getElementById('lifecycle-chart-wrapper');
    const canvas = document.getElementById('cocoon-analytics-lifecycle');
    const periodSelector = document.getElementById('lifecycle-period-selector');
    const periodButtons = document.querySelectorAll('.lifecycle-period-btn');
    const customRangeContainer = document.getElementById('lifecycle-custom-range'); // カスタム期間入力欄のコンテナ要素です
    const customFromInput = document.getElementById('lifecycle-custom-from'); // カスタム開始日（カレンダー入力）です
    const customToInput = document.getElementById('lifecycle-custom-to'); // カスタム終了日（カレンダー入力）です
    const listPeriodSelect = document.getElementById('lifecycle-list-period'); // 左リストの集計期間セレクトです
    const zoomNotice = document.getElementById('lifecycle-zoom-notice'); // グラフが別期間表示中であることを知らせるバッジです
    const zoomNoticeText = document.getElementById('lifecycle-zoom-notice-text'); // バッジ内のメッセージ表示部分です
    const zoomResetLink = document.getElementById('lifecycle-zoom-reset'); // リスト期間に戻すためのリンクです
    const summaryEl = document.getElementById('lifecycle-summary'); // 期間合計PVのサマリー表示要素です

    // 検索・ページング・ローディングに関する状態変数
    let currentPage = 1;
    let currentSearch = '';
    let isLoading = false;
    let hasMore = true;
    let searchTimeout = null;
    let isFirstLoad = true; // 初回読み込み時かどうかの判定用フラグです

    // カレンダーピッカーの初期値や入力可能な最小・最大日付を設定する関数です
    function setupCustomDatePickers(postDateStr, lifecycleData) {
      if (!customFromInput || !customToInput) {return;}

      const todayStr = new Date().toISOString().split('T')[0];
      const startDateStr = postDateStr || (lifecycleData && lifecycleData[0] ? lifecycleData[0].date : todayStr);
      const endDateStr = (lifecycleData && lifecycleData[lifecycleData.length - 1]) ? lifecycleData[lifecycleData.length - 1].date : todayStr;

      // フォームの初期値として公開日と最後のデータ日付を設定します
      customFromInput.value = startDateStr;
      customToInput.value = endDateStr;

      // 入力制限（選択範囲）を設定します
      customFromInput.min = startDateStr;
      customFromInput.max = todayStr;
      customToInput.min = startDateStr;
      customToInput.max = todayStr;
    }

    // アクセス生データから、指定された期間（直近N日）またはカスタム期間のデータだけを切り出すための関数です
    function filterDataByPeriod(data, period) {
      // データが存在しない場合は空配列を返します
      if (!data || !data.length) {return [];}

      // カスタム期間が選ばれている場合の処理です
      if (period === 'custom') {
        const fromVal = customFromInput.value;
        const toVal = customToInput.value;
        // どちらかの日付が入力されていない場合は、データを絞り込まずにそのまま返します
        if (!fromVal || !toVal) {return data;}

        const fromDate = new Date(fromVal);
        const toDate = new Date(toVal);
        fromDate.setHours(0, 0, 0, 0);
        toDate.setHours(23, 59, 59, 999);

        // 指定されたカスタム開始日から終了日までのデータを抽出します
        return data.filter((d) => {
          const dDate = new Date(d.date);
          dDate.setHours(0, 0, 0, 0);
          return dDate >= fromDate && dDate <= toDate;
        });
      }

      // 「全期間」が選択されている場合は、生データをそのまま返します
      if (period === 'all') {return data;}

      const days = parseInt(period, 10);
      // 日数の指定が正しく数値変換できない場合は、生データを返します
      if (isNaN(days)) {return data;}

      // 「直近N日」として、今日を含むN日分を切り出すための開始日・終了日を計算します
      const endDate = new Date();
      endDate.setHours(0, 0, 0, 0);
      const startDate = new Date(endDate.getTime());
      startDate.setDate(endDate.getDate() - (days - 1));

      // 直近N日の期間に含まれるデータだけを抽出して返します
      return data.filter((d) => {
        const dDate = new Date(d.date);
        dDate.setHours(0, 0, 0, 0);
        return dDate >= startDate && dDate <= endDate;
      });
    }

    // リスト集計期間の値（7days等）をグラフ期間チップの値（7等）へ変換します
    function listEquivPeriod() {
      const map = { '7days': '7', '30days': '30', '90days': '90', '1year': '365', 'all': 'all' };
      return map[listPeriod] || '30';
    }

    // 実際にグラフへ適用する期間を返します（ズーム中はその値、通常はリストに追従）
    function effectivePeriod() {
      return graphPeriodOverride || listEquivPeriod();
    }

    // 期間チップの値から表示用ラベル（ボタンの文言）を取得します
    function periodChipLabel(value) {
      let label = '';
      periodButtons.forEach((b) => {
        if (b.getAttribute('data-period') === value) {
          label = b.textContent.trim();
        }
      });
      return label;
    }

    // データ配列のPVを合計します
    function sumPV(arr) {
      return arr.reduce((sum, d) => { return sum + (parseInt(d.pv, 10) || 0); }, 0);
    }

    // 表示期間・リスト期間・累計の合計PVをサマリー行に表示します
    function updateSummary(filtered) {
      if (!summaryEl) {return;}
      // 記事未選択などデータが無い場合はサマリーを隠します
      if (!rawLifecycleData.length) {
        summaryEl.style.display = 'none';
        return;
      }
      const shownTotal = sumPV(filtered);
      const listTotal = sumPV(filterDataByPeriod(rawLifecycleData, listEquivPeriod()));
      const allTotal = sumPV(rawLifecycleData);
      summaryEl.textContent = (i18n.shown_period_total || '表示期間の合計') + ': ' + shownTotal.toLocaleString() + ' PV' +
        '（' + (i18n.list_period_total || 'リスト期間の合計') + ': ' + listTotal.toLocaleString() + ' PV / ' +
        (i18n.all_total || '累計') + ': ' + allTotal.toLocaleString() + ' PV）';
      summaryEl.style.display = 'block';
    }

    // 期間チップのアクティブ表示・バッジ・カスタム入力欄の表示状態をまとめて更新します
    function updatePeriodUI() {
      const eff = effectivePeriod();
      // 現在の実効期間に一致するチップだけをアクティブ表示にします
      periodButtons.forEach((b) => {
        b.classList.toggle('is-active', b.getAttribute('data-period') === eff);
      });
      // カスタム期間のときだけカレンダー入力欄を表示します
      if (customRangeContainer) {
        customRangeContainer.style.display = (eff === 'custom') ? 'flex' : 'none';
      }
      // リストと異なる期間を表示中の場合のみバッジを表示します
      if (zoomNotice && zoomNoticeText) {
        if (graphPeriodOverride && graphPeriodOverride !== listEquivPeriod()) {
          zoomNoticeText.textContent = (i18n.zoom_notice || 'グラフはリストと異なる期間を表示中') + '（' + periodChipLabel(eff) + '）';
          zoomNotice.style.display = 'flex';
        } else {
          zoomNotice.style.display = 'none';
        }
      }
    }

    // 現在の状態（実効期間）でグラフ・サマリー・期間UIをまとめて再描画します
    function refreshChart() {
      updatePeriodUI();
      // 記事が未選択でデータが無い場合はグラフ描画を行いません（初期プレースホルダーを維持）
      if (!rawLifecycleData.length) {
        updateSummary([]);
        return;
      }
      const filtered = filterDataByPeriod(rawLifecycleData, effectivePeriod());
      renderLifecycleChart(filtered);
      updateSummary(filtered);
    }

    // 取得した経過日数とPV数データから Chart.js を使って折れ線グラフを描画します
    function renderLifecycleChart(lifecycleData) {
      // 古いグラフが残っている場合は一度破棄して再作成できるようにします
      if (lifecycleChart) {
        lifecycleChart.destroy();
      }
      // データが無い場合はプレースホルダーを表示します
      if (!canvas || !lifecycleData || !lifecycleData.length) {
        // 記事選択後に呼ばれるため、「記事を選択してください」ではなく期間内にデータが無い旨の文面に差し替えます
        const placeholderText = chartPlaceholder ? chartPlaceholder.querySelector('p') : null;
        if (placeholderText) {
          placeholderText.textContent = i18n.no_period_data || '選択した期間のアクセスデータがありません。';
        }
        chartPlaceholder.style.display = 'block';
        chartWrapper.style.display = 'none';
        return;
      }

      chartPlaceholder.style.display = 'none';
      chartWrapper.style.display = 'block';

      // Chart.js インスタンスを作成し、データを流し込みます
      lifecycleChart = new Chart(canvas, {
        type: 'line',
        data: {
          // X軸のラベルを公開後日数から「実際の日付（date）」に変更します
          labels: lifecycleData.map((d) => { return d.date; }),
          datasets: [{
            label: i18n.pv || 'PV',
            data: lifecycleData.map((d) => { return d.pv; }),
            borderColor: palette[1],
            backgroundColor: 'rgba(234, 67, 53, 0.15)',
            fill: true,
            tension: 0.2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            // X軸のタイトルを「公開後日数」から「日付」へ変更します
            x: { title: { display: true, text: i18n.date || 'Date' } },
            y: { beginAtZero: true, ticks: { precision: 0 } }
          }
        }
      });
    }

    // サーバーから記事リスト（PV順、検索条件付き）を取得して描画します
    // keepSelectionにtrueを渡すと、選択中の記事を自動選択で上書きせずに維持します
    function loadPosts(append, keepSelection) {
      if (isLoading) {return;}
      isLoading = true;
      listLoader.style.display = 'block';

      // 追記（無限スクロール）でない場合はページ数を1に戻します
      if (!append) {
        currentPage = 1;
      } else {
        currentPage++;
      }

      // AJAXリクエスト用のXMLHttpRequestオブジェクトを作成します
      const xhr = new XMLHttpRequest();
      const query = 'action=cocoon_analytics_get_posts&nonce=' + encodeURIComponent(CA.ajax.lifecycle_nonce) +
                  '&s=' + encodeURIComponent(currentSearch) + '&page=' + currentPage +
                  '&period=' + encodeURIComponent(listPeriod);

      xhr.open('GET', CA.ajax.url + '?' + query, true);
      xhr.onload = function () {
        isLoading = false;
        listLoader.style.display = 'none';

        if (xhr.status >= 200 && xhr.status < 400) {
          const res = JSON.parse(xhr.responseText);
          if (res.success) {
            const posts = res.data.posts;
            hasMore = res.data.has_more;

            // 追記でなければ、リストを空にします
            if (!append) {
              postsList.innerHTML = '';
            }

            // 該当する記事が無い場合はメッセージを表示します
            if (posts.length === 0 && !append) {
              postsList.innerHTML = '<p style="padding:16px; color:#666; text-align:center;">該当する記事が見つかりませんでした。</p>';
              return;
            }

            // 取得した記事を一つずつリストに追加します
            posts.forEach((post) => {
              const item = document.createElement('div');
              item.className = 'lifecycle-post-item';
              item.setAttribute('data-post-id', post.post_id);

              // 遷移時のID指定・選択中の記事は最初からアクティブ表示にします
              if (initialPostId === post.post_id) {
                item.className += ' is-active';
                initialPostId = 0; // 一度アクティブにしたら初期IDのリセットを行います
              } else if (keepSelection && currentSelectedPostId === post.post_id) {
                // 期間切替による再読込でも、選択中の記事のハイライトを維持します
                item.className += ' is-active';
              }

              item.innerHTML = '<div class="lifecycle-post-title">' + escapeHtml(post.title) + '</div>' +
                               '<div class="lifecycle-post-meta">' +
                                 '<span class="lifecycle-post-date">' + escapeHtml(post.date) + '</span>' +
                                 '<span class="lifecycle-post-pv">' + escapeHtml(post.pv) + ' PV</span>' +
                               '</div>';

              // クリックした際にその記事のデータを取得するようにイベントを登録します
              item.addEventListener('click', () => {
                selectPost(post.post_id);
              });

              postsList.appendChild(item);
            });

            // リストに記事が1件以上存在する場合、1番上の記事を自動で選択状態にします
            if (!append && posts.length > 0) {
              let shouldAutoSelect = false;
              // 期間切替による再読込で選択中の記事がある場合は、自動選択せずグラフをそのまま維持します
              if (keepSelection && currentSelectedPostId > 0) {
                shouldAutoSelect = false;
              } else if (isFirstLoad) {
                // 初回ロード時は、他の画面から投稿IDが指定されて遷移してきた場合を除いて自動選択します
                const containerInitialId = parseInt(container.getAttribute('data-initial-post-id'), 10) || 0;
                if (containerInitialId === 0) {
                  shouldAutoSelect = true;
                }
              } else {
                // 検索実行時や検索のクリア時は、常に1番目の記事を自動選択します
                shouldAutoSelect = true;
              }

              if (shouldAutoSelect) {
                const activeItem = postsList.querySelector('.lifecycle-post-item.is-active');
                // 既にアクティブ表示になっている記事がなければ、1番目の記事を選択します
                if (!activeItem) {
                  selectPost(posts[0].post_id);
                }
              }
            }

            // 初回ロード完了なのでフラグをfalseに更新します
            isFirstLoad = false;
          }
        }
      };
      xhr.send();
    }

    // クロスサイトスクリプティング（XSS）対策のためのエスケープ処理です
    function escapeHtml(str) {
      if (!str) {return '';}
      return str.replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
    }

    // 記事リストで選択された記事のライフサイクルデータを読み込みます
    function selectPost(postId) {
      // 選択中の記事IDを記憶します（期間切替での選択維持に使用）
      currentSelectedPostId = postId;
      const items = postsList.querySelectorAll('.lifecycle-post-item');
      items.forEach((item) => {
        item.classList.remove('is-active');
        if (parseInt(item.getAttribute('data-post-id'), 10) === postId) {
          item.classList.add('is-active');
        }
      });

      chartTitle.textContent = '読み込み中...';

      const xhr = new XMLHttpRequest();
      const query = 'action=cocoon_analytics_get_lifecycle&nonce=' + encodeURIComponent(CA.ajax.lifecycle_nonce) +
                  '&post_id=' + postId;

      xhr.open('GET', CA.ajax.url + '?' + query, true);
      xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 400) {
          const res = JSON.parse(xhr.responseText);
          if (res.success) {
            chartTitle.textContent = res.data.title;
            // サーバーから返されたデータをそれぞれの状態変数に保存します
            rawLifecycleData = res.data.lifecycle;
            currentPostDate = res.data.post_date || '';

            // カレンダーピッカーの選択範囲と初期値を新しく設定します
            setupCustomDatePickers(currentPostDate, rawLifecycleData);

            // 記事が選択されたので、期間切り替え用のボタンを表示します
            if (periodSelector) {
              periodSelector.style.display = 'flex';
            }

            // 期間チップ・バッジ・サマリーを最新状態に更新してグラフを描画します
            refreshChart();
          } else {
            chartTitle.textContent = 'エラーが発生しました';
          }
        } else {
          chartTitle.textContent = '通信エラーが発生しました';
        }
      };
      xhr.send();
    }

    // 入力イベントに対してデバウンス（タイピング終了から300ms後に検索実行）を適用します
    searchInput.addEventListener('input', () => {
      clearTimeout(searchTimeout);
      currentSearch = searchInput.value.trim();

      if (currentSearch.length > 0) {
        clearBtn.style.display = 'block';
      } else {
        clearBtn.style.display = 'none';
      }

      searchTimeout = setTimeout(() => {
        loadPosts(false);
      }, 300);
    });

    // 検索ワードクリア用の「×」ボタンクリック時の処理です
    clearBtn.addEventListener('click', () => {
      searchInput.value = '';
      clearBtn.style.display = 'none';
      currentSearch = '';
      loadPosts(false);
    });

    // リストのスクロール量を監視し、最下部付近に達したら自動で次の20件を取得します
    postsList.addEventListener('scroll', () => {
      if (isLoading || !hasMore) {return;}
      const threshold = 50; // 下端から50px手前
      if (postsList.scrollHeight - postsList.clientHeight - postsList.scrollTop < threshold) {
        loadPosts(true);
      }
    });

    // 期間選択ボタンをクリックした際のアクションを設定します
    if (periodButtons.length > 0) {
      periodButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
          const value = btn.getAttribute('data-period');
          // リスト期間と同じ値を選んだ場合は「追従」に戻し、それ以外は一時的なズームとして上書きします
          graphPeriodOverride = (value === listEquivPeriod()) ? null : value;
          refreshChart();
        });
      });
    }

    // カスタム期間のカレンダー入力値が変更された場合のイベント処理です
    if (customFromInput && customToInput) {
      const onCustomDateChange = function () {
        // 開始日と終了日の順序が逆転した場合は自動で同じ日に補正します
        if (customFromInput.value && customToInput.value) {
          if (new Date(customFromInput.value) > new Date(customToInput.value)) {
            customToInput.value = customFromInput.value;
          }
        }
        // 現在カスタム期間表示モードであればグラフを再描画します
        if (effectivePeriod() === 'custom') {
          refreshChart();
        }
      };

      customFromInput.addEventListener('change', onCustomDateChange);
      customToInput.addEventListener('change', onCustomDateChange);
    }

    // リスト集計期間のセレクト変更時：リストを再取得し、グラフは追従状態に戻します
    if (listPeriodSelect) {
      // ブラウザのフォーム復元などで初期値が変わっている場合に備えて現在値を反映します
      listPeriod = listPeriodSelect.value || listPeriod;
      listPeriodSelect.addEventListener('change', () => {
        listPeriod = listPeriodSelect.value;
        // リスト期間を変えたら一時ズームは解除してグラフを新しい期間に追従させます
        graphPeriodOverride = null;
        loadPosts(false, true);
        refreshChart();
      });
    }

    // 「リスト期間に合わせる」リンクで一時ズームを解除します
    if (zoomResetLink) {
      zoomResetLink.addEventListener('click', (e) => {
        e.preventDefault();
        graphPeriodOverride = null;
        refreshChart();
      });
    }

    // PHP側で既に初期データが用意されている場合は、最初のグラフを描画します
    if (initialPostId > 0 && data.lifecycle && data.lifecycle.length) {
      rawLifecycleData = data.lifecycle;
      currentPostDate = data.post_date || '';

      // カレンダーピッカーの初期値・範囲制限を設定します
      setupCustomDatePickers(currentPostDate, rawLifecycleData);

      // 期間切り替えボタンを表示します
      if (periodSelector) {
        periodSelector.style.display = 'flex';
      }

      // 期間チップ・サマリーを含めて初回のグラフを描画します
      refreshChart();
    }
    // 最初の20件を読み込みます
    loadPosts(false);

  })();

  // ------------------------------------------------------------------
  // タイルのドラッグ&ドロップ（Sortable.js）
  // ------------------------------------------------------------------
  (function () {
    if (typeof window.Sortable === 'undefined') {return;}
    const board = document.querySelector('.cocoon-analytics-tiles');
    if (!board) {return;}
    const ajax = CA.ajax || {};
    if (!ajax.url || !ajax.nonce) {return;}

    const cols = board.querySelectorAll('.cocoon-analytics-tiles-col');
    const statusEl = document.querySelector('.cocoon-analytics-save-status');
    let saveTimer = null;

    function showStatus(text, isError) {
      if (!statusEl) {return;}
      statusEl.textContent = text || '';
      statusEl.classList.toggle('is-error', !!isError);
      if (text) {
        clearTimeout(statusEl._hideTimer);
        statusEl._hideTimer = setTimeout(() => { statusEl.textContent = ''; }, 2500);
      }
    }

    function collectLayout() {
      const out = [];
      board.querySelectorAll('.cocoon-analytics-tiles-col').forEach((col) => {
        const ids = [];
        col.querySelectorAll('.cocoon-analytics-tile').forEach((t) => {
          const id = t.getAttribute('data-tile-id');
          if (id) {ids.push(id);}
        });
        out.push(ids);
      });
      return out;
    }

    function saveLayout() {
      const payload = collectLayout();
      const fd = new FormData();
      fd.append('action', 'cocoon_analytics_save_layout');
      fd.append('nonce', ajax.nonce);
      fd.append('columns', JSON.stringify(payload));
      fetch(ajax.url, { method: 'POST', body: fd, credentials: 'same-origin' })
        .then((r) => { return r.json(); })
        .then((j) => {
          if (j && j.success) {showStatus(i18n.saved || 'Saved');}
          else {showStatus(i18n.save_failed || 'Failed', true);}
        })
        .catch(() => { showStatus(i18n.save_failed || 'Failed', true); });
    }

    function debouncedSave() {
      clearTimeout(saveTimer);
      saveTimer = setTimeout(saveLayout, 300);
    }

    cols.forEach((col) => {
      new Sortable(col, {
        group: 'cocoon-analytics-tiles',
        handle: '.cocoon-analytics-tile-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        // 端へドラッグした際のオートスクロール（ウィンドウ＋ボード両方）
        scroll: true,
        scrollSensitivity: 80,  // 端から 80px 以内に入ったらスクロール開始
        scrollSpeed: 18,        // スクロール速度（px/frame）
        bubbleScroll: true,     // 親要素や window もスクロール対象にする
        forceFallback: true,    // ネイティブDnDを無効化して fallback を使う（scroll が確実に効く）
        fallbackOnBody: true,
        fallbackTolerance: 4,
        onEnd: debouncedSave
      });
    });

    // 既定レイアウトに戻す
    const resetBtn = document.querySelector('.cocoon-analytics-reset-layout');
    if (resetBtn) {
      resetBtn.addEventListener('click', () => {
        if (!window.confirm(i18n.reset_confirm || 'Reset layout?')) {return;}
        const fd = new FormData();
        fd.append('action', 'cocoon_analytics_reset_layout');
        fd.append('nonce', ajax.nonce);
        fetch(ajax.url, { method: 'POST', body: fd, credentials: 'same-origin' })
          .then((r) => { return r.json(); })
          .then((j) => {
            if (j && j.success) {
              showStatus(i18n.saved || 'Saved');
              // 初心者向け: 再描画するためリロード（JSで並べ替えるより確実）
              window.location.reload();
            } else {
              showStatus(i18n.save_failed || 'Failed', true);
            }
          })
          .catch(() => { showStatus(i18n.save_failed || 'Failed', true); });
      });
    }
  })();

  // 著者・カテゴリの検索サジェスト機能
  (function () {
    // グローバルサジェストデータを取得します
    const suggestData = window.CocoonAnalytics && window.CocoonAnalytics.suggest ? window.CocoonAnalytics.suggest : null;
    if (!suggestData) {return;}

    const inputs = document.querySelectorAll('.cocoon-analytics-suggest-input');
    if (!inputs || !inputs.length) {return;}

    inputs.forEach((input) => {
      // 親コンテナと、対応する隠し（hidden）ID入力欄、ドロップダウン要素を取得します
      const container = input.closest('.cocoon-analytics-suggest-container');
      if (!container) {return;}

      const hiddenInput = container.querySelector('.cocoon-analytics-suggest-hidden');
      const dropdown = container.querySelector('.cocoon-analytics-suggest-dropdown');
      if (!hiddenInput || !dropdown) {return;}

      const type = input.getAttribute('data-type'); // 'author' または 'category'
      // サジェストのキーに対応するデータ配列を定義します
      const itemsSource = type === 'author' ? suggestData.authors : suggestData.categories;
      if (!itemsSource) {return;}

      // ドロップダウンを再構築して表示または非表示にする関数です
      function renderDropdown(keyword) {
        dropdown.innerHTML = '';
        keyword = keyword.trim().toLowerCase();

        // キーワードに部分一致するサジェスト項目をフィルタリングします
        const filtered = itemsSource.filter((item) => {
          return item.name.toLowerCase().indexOf(keyword) !== -1;
        });

        // 検索結果がない場合の表示です
        if (filtered.length === 0) {
          const noResults = document.createElement('div');
          noResults.className = 'cocoon-analytics-suggest-no-results';
          noResults.textContent = '一致する項目がありません';
          dropdown.appendChild(noResults);
        } else {
          // 候補リストを生成し、クリックイベントを設定します
          filtered.forEach((item) => {
            const el = document.createElement('div');
            el.className = 'cocoon-analytics-suggest-item';
            el.textContent = item.name;
            el.setAttribute('data-id', item.id);

            el.addEventListener('click', () => {
              input.value = item.name;
              hiddenInput.value = item.id;
              dropdown.style.display = 'none';
            });
            dropdown.appendChild(el);
          });
        }
        dropdown.style.display = 'block';
      }

      // 入力時のリアルタイムサジェスト処理です
      input.addEventListener('input', () => {
        const keyword = input.value;
        if (keyword.trim() === '') {
          // 入力が空の場合はIDを0（すべて）にリセットしてドロップダウンを閉じます
          hiddenInput.value = '0';
          dropdown.style.display = 'none';
          return;
        }
        renderDropdown(keyword);
      });

      // フォーカス時に現在値に応じてリストを表示します
      input.addEventListener('focus', () => {
        renderDropdown(input.value);
      });

      // 入力完了後（フォーカスアウト時）の無効入力値チェック処理です
      input.addEventListener('blur', () => {
        // 少し遅延させないと、ドロップダウンをクリックしたイベントが発火する前にリストが閉じてしまいます
        setTimeout(() => {
          dropdown.style.display = 'none';

          const val = input.value.trim();
          if (val === '') {
            hiddenInput.value = '0';
            return;
          }

          // 入力された文字列が、既存の著者・カテゴリのいずれとも完全一致しない場合はクリアします
          const match = itemsSource.find((item) => {
            return item.name.toLowerCase() === val.toLowerCase();
          });

          if (match) {
            input.value = match.name;
            hiddenInput.value = match.id;
          } else {
            // 無効な文字列の場合は入力をクリアしてIDを0（すべて）にリセットします
            input.value = '';
            hiddenInput.value = '0';
          }
        }, 200);
      });
    });

    // 検索入力欄でエンターキーが押されたときにドロップダウンを閉じるようにします
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        const active = document.activeElement;
        if (active && active.classList.contains('cocoon-analytics-suggest-input')) {
          // フォーカスを外してドロップダウンを閉じる処理を走らせます
          active.blur();
        }
      }
    });
  })();
})();
