// obtain tab labels from DOM
const tabLabels = document.querySelectorAll('.tab-label');
// obtain tab contents from DOM
const tabContents = document.querySelectorAll('.tab-content');

// select first tab
if (tabLabels.length > 0) {
  tabLabels[0].classList.add("is-active");
  tabContents[0].classList.add("is-active");
}

// add click event to each tab label
tabLabels.forEach((tabLabel) => {
  tabLabel.addEventListener('click', tabChanged);
})

// switch tab
function tabChanged(e) {
  // remove is-active from label and content
  tabLabels.forEach((tabLabel) => {
    tabLabel.classList.remove("is-active");
  });
  tabContents.forEach((tabContent) => {
    tabContent.classList.remove("is-active");
  });

  // check which label is clicked
  const target = e.target;

  var index;
  for (index = 0; index < tabLabels.length; index++) {
    if (target === tabLabels[index]) {
      break;
    }
  }

  // add is-active to label and content
  tabLabels[index].classList.add("is-active");
  tabContents[index].classList.add("is-active");
}
