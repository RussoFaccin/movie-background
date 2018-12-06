var frame = wp.media({
    title: 'Select or Upload Media',
    button: {
      text: 'Use this media'
    },
    multiple: false  // Set to true to allow multiple files to be selected
});

var videoUrl = document.querySelector('.a-fldVideoUrl');

// Upload Button
var btnUpload = document.querySelector('.a-videoUpload');
var videoPlaceholder = document.querySelector('.a-videoPlaceholder');
var videoSource = document.querySelector('.a-videoPlaceholder__source');
console.log(videoPlaceholder);
btnUpload.addEventListener('click', (evt) => {
    evt.preventDefault();
    frame.open();
});

frame.on('select', function(evt) {
    var attachment = frame.state().get('selection').first().toJSON().url;
    videoUrl.value = attachment;
    videoPlaceholder.src = attachment;
    videoPlaceholder.appendChild(videoSource);
});

var sectionCaptions = document.querySelector('.m-sectionCaptions');

// Add new item

var btnAddNew = document.querySelector('.a-addNewCaption');
btnAddNew.addEventListener('click', (evt) => {
    evt.preventDefault();
    var indexAtual = document.querySelectorAll('.m-sectionCaptions__item').length;
    var elementItem = document.querySelector('.m-sectionCaptions__item');
    var clone = elementItem.cloneNode(true);
    
    for (let i = 0; i < clone.childNodes.length; i++) {
        clone.childNodes[i].name = clone.childNodes[i].name.replace(/\[\d\]/gi, `[${indexAtual}]`);
        clone.childNodes[i].value = '';

        if (clone.childNodes[i].localName == 'button') {
            clone.childNodes[i].addEventListener('click', attachDeleteHandler);
        }
    }

    sectionCaptions.appendChild(clone);
});

// Delete item

var btnsDelete = document.querySelectorAll('.m-sectionCaptions__itemDelete');
btnsDelete.forEach((item, index) => {
    item.addEventListener('click', attachDeleteHandler);
});

function attachDeleteHandler(evt) {
    evt.preventDefault();
    var itemsList = document.querySelector('.m-sectionCaptions');
    var indexToDelete = evt.target.dataset.index;
    itemsList.removeChild(itemsList.childNodes[indexToDelete]);
    rearrangeIndexItems(itemsList);
}

function rearrangeIndexItems(listItems) {
    for (let i = 0; i < listItems.childNodes.length; i++) {
        changeElementIndex(listItems.childNodes[i], i);
        // listItems.childNodes[i].innerHTML = changeElementIndex(listItems.childNodes[i].innerHTML, i)
    }
}

function changeElementIndex(element, index) {
    for (let i = 0; i < element.childNodes.length; i++) {
        var replaced = element.childNodes[i].outerHTML.replace(/\[\d\]/g, `[${index}]`);
        element.childNodes[i].outerHTML = replaced;
    }
    // console.log('REPLACED: ', replaced);
    // console.log('INDEX: ', index);
    // return replaced;
}