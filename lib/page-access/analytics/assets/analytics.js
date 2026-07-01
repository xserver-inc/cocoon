/**
 * Cocoon Analytics Dashboard - Chart rendering
 * 依存: Chart.js (UMD), CocoonAnalytics (wp_localize_script)
 */
(function () {
  'use strict';

  if (typeof window.Chart === 'undefined' || typeof window.CocoonAnalytics === 'undefined') {
    return;
  }

  var CA = window.CocoonAnalytics;
  var data = CA.data || {};
  var i18n = CA.i18n || {};

  // 共通カラーパレット
  var palette = [
    '#4285F4', '#EA4335', '#FBBC04', '#34A853', '#673AB7',
    '#00ACC1', '#FF7043', '#AB47BC', '#26A69A', '#5C6BC0'
  ];

  // ドーナツ/パイの共通オプション（凡例を右に表示してグラフを潰さない）
  var doughnutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'right',
        labels: {
          boxWidth: 14,
          font: { size: 11 },
          padding: 8
        }
      }
    }
  };

  // カスタム期間の表示切替
  var periodSelect = document.querySelector('.cocoon-analytics-period-form select[name="period"]');
  if (periodSelect) {
    periodSelect.addEventListener('change', function () {
      var range = document.querySelector('.cocoon-analytics-period-form .cocoon-analytics-custom-range');
      if (range) {
        range.style.display = periodSelect.value === 'custom' ? '' : 'none';
      }
    });
  }

  // 日次PV推移
  (function () {
    var canvas = document.getElementById('cocoon-analytics-daily');
    if (!canvas || !data.daily || !data.daily.length) return;
    new Chart(canvas, {
      type: 'line',
      data: {
        labels: data.daily.map(function (d) { return d.date; }),
        datasets: [{
          label: i18n.pv || 'PV',
          data: data.daily.map(function (d) { return d.pv; }),
          borderColor: palette[0],
          backgroundColor: 'rgba(66, 133, 244, 0.15)',
          fill: true,
          tension: 0.25,
          pointRadius: data.daily.length > 60 ? 0 : 3
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

  // 週次PV推移
  (function () {
    var canvas = document.getElementById('cocoon-analytics-weekly');
    if (!canvas || !data.weekly || !data.weekly.length) return;
    new Chart(canvas, {
      type: 'bar',
      data: {
        labels: data.weekly.map(function (d) { return d.date; }),
        datasets: [{
          label: i18n.pv || 'PV',
          data: data.weekly.map(function (d) { return d.pv; }),
          backgroundColor: 'rgba(52, 168, 83, 0.7)',
          borderColor: '#34A853'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              afterLabel: function (ctx) {
                var row = data.weekly[ctx.dataIndex];
                if (row && row.days && row.days < 7) {
                  return (i18n.partial_week || '部分週') + ': ' + row.days + '/7 ' + (i18n.days || '日');
                }
                return '';
              }
            }
          }
        },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
      }
    });
  })();

  // 月次PV推移
  (function () {
    var canvas = document.getElementById('cocoon-analytics-monthly');
    if (!canvas || !data.monthly || !data.monthly.length) return;
    new Chart(canvas, {
      type: 'bar',
      data: {
        labels: data.monthly.map(function (d) { return d.date; }),
        datasets: [{
          label: i18n.pv || 'PV',
          data: data.monthly.map(function (d) { return d.pv; }),
          backgroundColor: 'rgba(251, 188, 4, 0.7)',
          borderColor: '#FBBC04'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              afterLabel: function (ctx) {
                var row = data.monthly[ctx.dataIndex];
                if (row && row.days) {
                  // 初心者向け: その月の実日数を表示。フル月なら 28～31、部分月なら < 28
                  return (i18n.days_count || '日数') + ': ' + row.days + ' ' + (i18n.days || '日');
                }
                return '';
              }
            }
          }
        },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
      }
    });
  })();

  // 投稿タイプ別
  (function () {
    var canvas = document.getElementById('cocoon-analytics-type');
    if (!canvas || !data.by_type || !data.by_type.length) return;
    new Chart(canvas, {
      type: 'doughnut',
      data: {
        labels: data.by_type.map(function (d) { return d.post_type; }),
        datasets: [{
          data: data.by_type.map(function (d) { return d.pv; }),
          backgroundColor: palette
        }]
      },
      options: doughnutOptions
    });
  })();

  // カテゴリ別（ダッシュボード用: 上位10 + その他）
  (function () {
    var canvas = document.getElementById('cocoon-analytics-category');
    if (!canvas || !data.by_category || !data.by_category.length) return;
    new Chart(canvas, {
      type: 'doughnut',
      data: {
        labels: data.by_category.map(function (d) { return d.name; }),
        datasets: [{
          data: data.by_category.map(function (d) { return d.pv; }),
          backgroundColor: palette
        }]
      },
      options: doughnutOptions
    });
  })();

  // タグ別（ダッシュボード用: 上位10 + その他）
  (function () {
    var canvas = document.getElementById('cocoon-analytics-tag');
    if (!canvas || !data.by_tag || !data.by_tag.length) return;
    new Chart(canvas, {
      type: 'doughnut',
      data: {
        labels: data.by_tag.map(function (d) { return d.name; }),
        datasets: [{
          data: data.by_tag.map(function (d) { return d.pv; }),
          backgroundColor: palette
        }]
      },
      options: doughnutOptions
    });
  })();

  // 曜日別
  (function () {
    var canvas = document.getElementById('cocoon-analytics-dow');
    if (!canvas || !data.by_dow || !data.by_dow.length) return;
    var weekdays = CA.weekdays || ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    new Chart(canvas, {
      type: 'bar',
      data: {
        labels: data.by_dow.map(function (d) { return weekdays[d.dow - 1]; }),
        datasets: [{
          label: i18n.pv || 'PV',
          data: data.by_dow.map(function (d) { return d.pv; }),
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
    var canvas = document.getElementById('cocoon-analytics-terms');
    if (!canvas || !data.terms || !data.terms.length) return;
    new Chart(canvas, {
      type: 'pie',
      data: {
        labels: data.terms.map(function (d) { return d.name; }),
        datasets: [{
          data: data.terms.map(function (d) { return d.pv; }),
          backgroundColor: palette
        }]
      },
      options: { responsive: true, maintainAspectRatio: false }
    });
  })();

  // 著者別（上位10）
  (function () {
    var canvas = document.getElementById('cocoon-analytics-authors');
    if (!canvas || !data.authors || !data.authors.length) return;
    new Chart(canvas, {
      type: 'bar',
      data: {
        labels: data.authors.map(function (d) { return d.name; }),
        datasets: [{
          label: i18n.pv || 'PV',
          data: data.authors.map(function (d) { return d.pv; }),
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
    var container = document.querySelector('.cocoon-analytics-lifecycle-container');
    if (!container) return;

    // URLから渡された初期の投稿IDを取得します（初期値は0）
    var initialPostId = parseInt(container.getAttribute('data-initial-post-id'), 10) || 0;
    var lifecycleChart = null;

    // HTML上の各DOM要素を取得します
    var searchInput = document.getElementById('lifecycle-search-input');
    var postsList = document.getElementById('lifecycle-posts-list');
    var listLoader = document.getElementById('lifecycle-list-loader');
    var clearBtn = document.getElementById('clear-search-btn');
    var chartTitle = document.getElementById('lifecycle-chart-title');
    var chartPlaceholder = document.getElementById('lifecycle-chart-placeholder');
    var chartWrapper = document.getElementById('lifecycle-chart-wrapper');
    var canvas = document.getElementById('cocoon-analytics-lifecycle');

    // 検索・ページング・ローディングに関する状態変数
    var currentPage = 1;
    var currentSearch = '';
    var isLoading = false;
    var hasMore = true;
    var searchTimeout = null;

    // 取得した経過日数とPV数データから Chart.js を使って折れ線グラフを描画します
    function renderLifecycleChart(lifecycleData) {
      // 古いグラフが残っている場合は一度破棄して再作成できるようにします
      if (lifecycleChart) {
        lifecycleChart.destroy();
      }
      // データが無い場合はプレースホルダーを表示します
      if (!canvas || !lifecycleData || !lifecycleData.length) {
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
          labels: lifecycleData.map(function (d) { return d.day_after; }),
          datasets: [{
            label: i18n.pv || 'PV',
            data: lifecycleData.map(function (d) { return d.pv; }),
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
            x: { title: { display: true, text: i18n.dayAfter || 'Days after publish' } },
            y: { beginAtZero: true, ticks: { precision: 0 } }
          }
        }
      });
    }

    // サーバーから記事リスト（PV順、検索条件付き）を取得して描画します
    function loadPosts(append) {
      if (isLoading) return;
      isLoading = true;
      listLoader.style.display = 'block';

      // 追記（無限スクロール）でない場合はページ数を1に戻します
      if (!append) {
        currentPage = 1;
      } else {
        currentPage++;
      }

      // AJAXリクエスト用のXMLHttpRequestオブジェクトを作成します
      var xhr = new XMLHttpRequest();
      var query = 'action=cocoon_analytics_get_posts&nonce=' + encodeURIComponent(CA.ajax.lifecycle_nonce) +
                  '&s=' + encodeURIComponent(currentSearch) + '&page=' + currentPage;
      
      xhr.open('GET', CA.ajax.url + '?' + query, true);
      xhr.onload = function () {
        isLoading = false;
        listLoader.style.display = 'none';

        if (xhr.status >= 200 && xhr.status < 400) {
          var res = JSON.parse(xhr.responseText);
          if (res.success) {
            var posts = res.data.posts;
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
            posts.forEach(function (post) {
              var item = document.createElement('div');
              item.className = 'lifecycle-post-item';
              item.setAttribute('data-post-id', post.post_id);
              
              // 遷移時にID指定されていた場合は最初からアクティブ表示にします
              if (initialPostId === post.post_id) {
                item.className += ' is-active';
                initialPostId = 0; // 一度アクティブにしたら初期IDのリセットを行います
              }

              item.innerHTML = '<div class="lifecycle-post-title">' + escapeHtml(post.title) + '</div>' +
                               '<div class="lifecycle-post-meta">' +
                                 '<span class="lifecycle-post-date">' + escapeHtml(post.date) + '</span>' +
                                 '<span class="lifecycle-post-pv">' + escapeHtml(post.pv) + ' PV</span>' +
                               '</div>';

              // クリックした際にその記事のデータを取得するようにイベントを登録します
              item.addEventListener('click', function () {
                selectPost(post.post_id);
              });

              postsList.appendChild(item);
            });
          }
        }
      };
      xhr.send();
    }

    // クロスサイトスクリプティング（XSS）対策のためのエスケープ処理です
    function escapeHtml(str) {
      if (!str) return '';
      return str.replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
    }

    // 記事リストで選択された記事のライフサイクルデータを読み込みます
    function selectPost(postId) {
      var items = postsList.querySelectorAll('.lifecycle-post-item');
      items.forEach(function (item) {
        item.classList.remove('is-active');
        if (parseInt(item.getAttribute('data-post-id'), 10) === postId) {
          item.classList.add('is-active');
        }
      });

      chartTitle.textContent = '読み込み中...';

      var xhr = new XMLHttpRequest();
      var query = 'action=cocoon_analytics_get_lifecycle&nonce=' + encodeURIComponent(CA.ajax.lifecycle_nonce) +
                  '&post_id=' + postId;

      xhr.open('GET', CA.ajax.url + '?' + query, true);
      xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 400) {
          var res = JSON.parse(xhr.responseText);
          if (res.success) {
            chartTitle.textContent = res.data.title;
            renderLifecycleChart(res.data.lifecycle);
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
    searchInput.addEventListener('input', function () {
      clearTimeout(searchTimeout);
      currentSearch = searchInput.value.trim();

      if (currentSearch.length > 0) {
        clearBtn.style.display = 'block';
      } else {
        clearBtn.style.display = 'none';
      }

      searchTimeout = setTimeout(function () {
        loadPosts(false);
      }, 300);
    });

    // 検索ワードクリア用の「×」ボタンクリック時の処理です
    clearBtn.addEventListener('click', function () {
      searchInput.value = '';
      clearBtn.style.display = 'none';
      currentSearch = '';
      loadPosts(false);
    });

    // リストのスクロール量を監視し、最下部付近に達したら自動で次の20件を取得します
    postsList.addEventListener('scroll', function () {
      if (isLoading || !hasMore) return;
      var threshold = 50; // 下端から50px手前
      if (postsList.scrollHeight - postsList.clientHeight - postsList.scrollTop < threshold) {
        loadPosts(true);
      }
    });

    // PHP側で既に初期データが用意されている場合は、最初のグラフを描画します
    if (initialPostId > 0 && data.lifecycle && data.lifecycle.length) {
      renderLifecycleChart(data.lifecycle);
    }
    // 最初の20件を読み込みます
    loadPosts(false);

  })();

  // ------------------------------------------------------------------
  // タイルのドラッグ&ドロップ（Sortable.js）
  // ------------------------------------------------------------------
  (function () {
    if (typeof window.Sortable === 'undefined') return;
    var board = document.querySelector('.cocoon-analytics-tiles');
    if (!board) return;
    var ajax = CA.ajax || {};
    if (!ajax.url || !ajax.nonce) return;

    var cols = board.querySelectorAll('.cocoon-analytics-tiles-col');
    var statusEl = document.querySelector('.cocoon-analytics-save-status');
    var saveTimer = null;

    function showStatus(text, isError) {
      if (!statusEl) return;
      statusEl.textContent = text || '';
      statusEl.classList.toggle('is-error', !!isError);
      if (text) {
        clearTimeout(statusEl._hideTimer);
        statusEl._hideTimer = setTimeout(function () { statusEl.textContent = ''; }, 2500);
      }
    }

    function collectLayout() {
      var out = [];
      board.querySelectorAll('.cocoon-analytics-tiles-col').forEach(function (col) {
        var ids = [];
        col.querySelectorAll('.cocoon-analytics-tile').forEach(function (t) {
          var id = t.getAttribute('data-tile-id');
          if (id) ids.push(id);
        });
        out.push(ids);
      });
      return out;
    }

    function saveLayout() {
      var payload = collectLayout();
      var fd = new FormData();
      fd.append('action', 'cocoon_analytics_save_layout');
      fd.append('nonce', ajax.nonce);
      fd.append('columns', JSON.stringify(payload));
      fetch(ajax.url, { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (j) {
          if (j && j.success) showStatus(i18n.saved || 'Saved');
          else showStatus(i18n.save_failed || 'Failed', true);
        })
        .catch(function () { showStatus(i18n.save_failed || 'Failed', true); });
    }

    function debouncedSave() {
      clearTimeout(saveTimer);
      saveTimer = setTimeout(saveLayout, 300);
    }

    cols.forEach(function (col) {
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
    var resetBtn = document.querySelector('.cocoon-analytics-reset-layout');
    if (resetBtn) {
      resetBtn.addEventListener('click', function () {
        if (!window.confirm(i18n.reset_confirm || 'Reset layout?')) return;
        var fd = new FormData();
        fd.append('action', 'cocoon_analytics_reset_layout');
        fd.append('nonce', ajax.nonce);
        fetch(ajax.url, { method: 'POST', body: fd, credentials: 'same-origin' })
          .then(function (r) { return r.json(); })
          .then(function (j) {
            if (j && j.success) {
              showStatus(i18n.saved || 'Saved');
              // 初心者向け: 再描画するためリロード（JSで並べ替えるより確実）
              window.location.reload();
            } else {
              showStatus(i18n.save_failed || 'Failed', true);
            }
          })
          .catch(function () { showStatus(i18n.save_failed || 'Failed', true); });
      });
    }
  })();
})();
