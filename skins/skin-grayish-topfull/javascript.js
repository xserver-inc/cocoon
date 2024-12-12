// スキングレイッシュ独自機能
// ---------------------------------------------
// スマートフォンなどのタッチデバイスで:hoverが微妙な見た目になることの対策
// 参考:https://www.webdesignleaves.com/pr/plugins/ontouchstart-event-handler.html
// ---------------------------------------------
// 空の ontouchstart 属性を body 要素に設定
document.getElementsByTagName('body')[0].setAttribute('ontouchstart', '');

// ---------------------------------------------
// navi sub-menu の表示OFF
// Front,Archives,Other
// .skin-grayish .navi-in>ul>li>.sub-menu
// ---------------------------------------------

// PC navi
const PCNaviIn = document.querySelector(".skin-grayish #navi-in");

// submenuがあるかどうか
const PCsubmenu = document.querySelectorAll(".skin-grayish .navi-in>ul>li:not(.header-snsicon-submenu)>.sub-menu");
const numberOfElements = PCsubmenu.length;
const submenuArray = Array.from(PCsubmenu);

// submenu Scrolltype
const AllPageSubmenuScrolltype = (ActiveElement) => {
  const headerElement = ActiveElement;
  const scrollPosition = window.scrollY;
  const scrollThreshold = 300;

  if (scrollPosition > scrollThreshold) {
    headerElement.setAttribute(
      "data-active", "ture"
    );

  } else {
    headerElement.setAttribute(
      "data-active", "false"
    );
  }
};


// submenuが画面右端からはみ出る場合の処理
const PCsubmenuRightCare = (event) => {
  const windowWidth = window.innerWidth;

  submenuArray.forEach((submenuItem, index) => {
    const itemRect = submenuItem.getBoundingClientRect();
    const itemAnchorElement = submenuItem.querySelector('li > a');
    if (itemAnchorElement) {
      const itemComputedStyle = window.getComputedStyle(itemAnchorElement);
      const itemPaddingLeft = parseFloat(itemComputedStyle.paddingLeft);
      const itemPaddingRight = parseFloat(itemComputedStyle.paddingRight);

      const itemRectRight = itemRect.right + (itemPaddingRight * 4);
      const itemWidthTwice = (itemRect.width * 1.2) + itemPaddingLeft + itemPaddingRight;

      if (itemRectRight > windowWidth) {
        submenuItem.style.left = 'auto';
      } else {
        if ((windowWidth - itemRectRight) > itemWidthTwice) {
          submenuItem.style.left = '';
        }
      }
    }
  });
}

let PCsubmenuRightCare_waitTimer;
const ResizePCsubmenuRightCare = (event) => {
  clearTimeout(PCsubmenuRightCare_waitTimer);
  PCsubmenuRightCare_waitTimer = setTimeout(function () {
    PCsubmenuRightCare(event);
  }, 100);

}
const loadPCsubmenuRightCare = (event) => {
  PCsubmenuRightCare(event);
};

const SubmenuScrolltype = () => {
  AllPageSubmenuScrolltype(PCNaviIn);
};

// ---------------------------------------------
// 画面幅1023px以下ではIntersectionObserver監視しないようにする
// ---------------------------------------------

const mediaQueryList1023 = window.matchMedia('(max-width: 1023px)');
function headerSubmenuOffChange(e) {
  if (e.matches) {
    window.removeEventListener('load', loadPCsubmenuRightCare);
    window.removeEventListener('resize', ResizePCsubmenuRightCare);
    window.removeEventListener('scroll', SubmenuScrolltype);

  } else {
    window.addEventListener('load', loadPCsubmenuRightCare);
    window.addEventListener('resize', ResizePCsubmenuRightCare);
    window.addEventListener('scroll', SubmenuScrolltype);

    loadPCsubmenuRightCare();
    SubmenuScrolltype();

  }
  mediaQueryList1023.addEventListener("change", headerSubmenuOffChange);
}
if (numberOfElements > 0) {
  headerSubmenuOffChange(mediaQueryList1023);
}

// ---------------------------------------------
// 投稿or固定ページのSNSシェアボタン
// 指定セクション(main)で表示
// ---------------------------------------------
let Ftobserver;
let flagObserver;

const scrolDispOff_snsshare = (dispOffSection, targetItem) => {

  const targetOffItem = targetItem;
  const targetSection = dispOffSection;

  if (!targetOffItem || !targetSection) return;

  const doWhenIntersect = (entries, obs) => {
    if (flagObserver === 'false') {
      obs.unobserve(entries[0].target);
    } else {
      if (entries[0].isIntersecting) {
        activateTargetOn();
      } else {
        activateTargetOff();
      }
    }
  };

  const options = {
    root: null,
    // rootMargin: "0%",
    rootMargin: "-50% 0px", // ビューポートの中心を判定基準にする
    threshold: 0
  };

  Ftobserver = new IntersectionObserver(doWhenIntersect, options);

  Ftobserver.observe(targetSection);

  const activateTargetOff = () => {
    if (targetOffItem.dataset.scroldisp === 'on') {
      targetOffItem.dataset.scroldisp = 'off';
    }
  };
  const activateTargetOn = () => {
    if (targetOffItem.dataset.scroldisp === 'off') {
      targetOffItem.dataset.scroldisp = 'on';
    }
  };

};
const footerOffItem = document.querySelector('.skin-grayish .article-header .sns-share');
// const scrolOffSection = document.getElementById('footer');
const scrolOffSection = document.getElementById('main');

// ---------------------------------------------
// 画面幅1400px以下ではIntersectionObserver監視しないようにする
// ---------------------------------------------

const mediaQueryList1400 = window.matchMedia('(max-width: 1400px)');
function footerOffChange(e) {
  if (e.matches) {
    footerOffItem.setAttribute('data-scroldisp', 'on');
    flagObserver = 'false';

  } else {
    footerOffItem.setAttribute('data-scroldisp', 'off');
    flagObserver = 'true';
    scrolDispOff_snsshare(scrolOffSection, footerOffItem);
  }
  mediaQueryList1400.addEventListener("change", footerOffChange);
}
if (footerOffItem) {
  footerOffChange(mediaQueryList1400);
}

// ---------------------------------------------
// breadcrumb 画面幅834px　〜　1400px以下
// ---------------------------------------------
const mediaQueryList834to1400 = window.matchMedia('(min-width: 835px) and (max-width: 1400px)');

const breadcrumb = document.querySelector('.skin-grayish .breadcrumb');
const mainContents = document.querySelector('.skin-grayish .content .main');
let mainContentsRect;
let timer_breadcrumb;

const breadcrumbLeftSet = () => {
  if (!breadcrumb) return;
  mainContentsRect = mainContents.getBoundingClientRect();
  mainContentsRect_x = Math.round(mainContentsRect.left);
  // for iPhone
  let safeLeft = getComputedStyle(document.documentElement).getPropertyValue('--safeareainsetleft');
  let safeLeftValue = safeLeft.split('px');

  mainContentsRect_x_after = mainContentsRect_x - safeLeftValue[0];
  breadcrumb.style.paddingLeft = mainContentsRect_x_after + 28 + 'px';

};

const breadcrumb_waitTime = 500;//for FireFox

const breadcrumbLeftReset = () => {
  if (!breadcrumb) return;
  breadcrumb.style.paddingLeft = '';
};

function breadcrumbLeftOn(e) {
  if (e.matches) {
    breadcrumbLeftSet();
    window.addEventListener('load', breadcrumbLeftSet);
    window.addEventListener('resize', breadcrumbLeftSet);

  } else {
    window.removeEventListener('load', breadcrumbLeftSet);
    window.removeEventListener('resize', breadcrumbLeftSet);

    setTimeout(breadcrumbLeftReset, breadcrumb_waitTime);

    // breadcrumbLeftReset();
  }
  mediaQueryList834to1400.addEventListener("change", breadcrumbLeftOn);
}

if (breadcrumb) {
  breadcrumbLeftOn(mediaQueryList834to1400);
}

// ---------------------------------------------
// フロントページタイプ：カテゴリーごと
// 表示カテゴリーが設定されていない場合疑似要素を非表示にする
// ---------------------------------------------
const listColumns_Category = document.querySelector('.skin-grayish .front-page-type-category .list-columns');
const listColumns_Category2col = document.querySelector('.skin-grayish .front-page-type-category-2-columns .list-columns');
const listColumns_Category3col = document.querySelector('.skin-grayish .front-page-type-category-3-columns .list-columns');

const listColumns_Selectors = [
  '.skin-grayish .front-page-type-category .list-columns',
  '.skin-grayish .front-page-type-category-2-columns .list-columns',
  '.skin-grayish .front-page-type-category-3-columns .list-columns'
];
const listColumns_CategoryType_childChk = () => {
  listColumns_Selectors.forEach((childChk_target) => {
    const listColumns_Category_target = document.querySelector(childChk_target);

    if (listColumns_Category_target) {
      const numberlistColumns_child = listColumns_Category_target.childElementCount;

      if (numberlistColumns_child < 1) {
        document.documentElement.style.setProperty('--listColumns_Category_displayOn', `none`);
        document.documentElement.style.setProperty('--listColumns_Category_23cols', `0rem`);
      } else {
        document.documentElement.style.setProperty('--listColumns_Category_displayOn', `block`);
        document.documentElement.style.setProperty('--listColumns_Category_23cols', `5rem`);
      }
    }
  });
};
if (listColumns_Category || listColumns_Category2col || listColumns_Category3col) {
  listColumns_CategoryType_childChk();
}


// ---------------------------------------------
// Front以外のページについて（PC時）
// グローバルメニューの折り返し時にメニューの高さを変更する
// ---------------------------------------------
const PCotherHeader = document.querySelector(".skin-grayish:not(.front-top-page) #header-in");

const PCotherNaviIn = document.querySelector(".skin-grayish:not(.front-top-page) .navi-in .menu-top.menu-header.menu-pc");

const PCotherNaviInList = document.querySelectorAll(".skin-grayish:not(.front-top-page) .navi-in>ul>li");

const otherPageHeaderWrap = () => {
  if (!PCotherNaviIn || !PCotherHeader) return;

  const PCotherHeader_Height = PCotherHeader.clientHeight;
  const PCotherNaviIn_Height = PCotherNaviIn.clientHeight;
  // console.log("PCotherHeader_Height: " + PCotherHeader_Height);
  // console.log("PCotherNaviIn_Height: " + PCotherNaviIn_Height);

  if (PCotherNaviIn_Height > PCotherHeader_Height) {
    // Naviの方がHeaderより高いとき : flex wrapしたとき
    PCotherNaviInList.forEach((item) => {
      item.style.height = 'auto';
    });
  } else {
    PCotherNaviInList.forEach((item) => {
      item.style.height = '100%';
    });
  }
};
const otherNavi_HeightResize = (flag) => {
  if (flag === 'true') {
    window.addEventListener('load', otherPageHeaderWrap);
    window.addEventListener('resize', otherPageHeaderWrap);
    otherPageHeaderWrap();
  } else {
    // 指定サイズ以下ではイベント削除
    window.removeEventListener('load', otherPageHeaderWrap);
    window.removeEventListener('resize', otherPageHeaderWrap);
  }
};

function otherNaviIn_Ctrl(e) {
  if (e.matches) {
    otherNavi_HeightResize('false');

  } else {
    otherNavi_HeightResize('true');

  }
  mediaQueryList1023.addEventListener("change", otherNaviIn_Ctrl);

}

if (PCotherHeader && PCotherNaviIn) {
  otherNaviIn_Ctrl(mediaQueryList1023);
}

// ---------------------------------------------
// 検索フォーム（PC時）
// ---------------------------------------------

const menuContent = document.querySelector(".header-container .navi .js-searchMenuDialog-content");
const menuOpen = document.querySelector(".header-container .navi .js-searchMenuDialog-open");
const menuClose = document.querySelector(".header-container .navi .js-searchMenuDialog-close");
const menuCloseBack = document.querySelector(".header-container .navi .js-searchMenuDialog-back");
const menuContentSearch = document.getElementById("gnavi-search-menu-content");

//
if (menuOpen) {
  menuOpen.addEventListener("click", async () => {
    menuContent.removeAttribute("style");
    menuContent.showModal();

  });
}
const closeMenu = () => {
  menuContent.close();
};
if (menuClose) {
  menuClose.addEventListener("click", closeMenu);
}
if (menuCloseBack) {
  menuCloseBack.addEventListener("click", closeMenu);
}
if (menuContent) {
  menuContent.addEventListener("close", async (e) => {
    await waitDialogAnimation(e.target)
    menuContent.style.display = "none";
  })
}

const waitDialogAnimation = (dialog) => Promise.allSettled(
  Array.from(dialog.getAnimations()).map(animation => animation.finished)
);
if (menuContentSearch) {
  menuContentSearch.addEventListener('click', (event) => {
    event.stopPropagation();
  });
}

mediaQueryList1023.addEventListener('change', (e) => {
  if (e.matches) {
    // 画面幅が1023px以下になったときにmenuContentを閉じる
    if (menuContent) {
      menuContent.close();
    }
  }
});


