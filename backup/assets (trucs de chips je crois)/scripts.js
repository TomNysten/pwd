$(function () {
  // $('[data-toggle="tooltip"]').tooltip();
  handleCollectionForm();

  });

function handleCollectionForm(){
  if ($('div[data-prototype]').length){

    var $collectionHolder;

    // setup an "add a tag" link
    var $addTagButton = $('<button type="button" class="add_tag_link">Add a tag</button>');
    // var $newLinkLi = $('<li></li>').append($addTagButton);



    // Get the ul that holds the collection of tags
    $collectionHolder = $('div[data-prototype]');

    // add the "add a tag" anchor and li to the tags ul
    $collectionHolder.append($addTagButton);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addTagButton.on('click', function(e) {
      // add a new tag form (see next code block)
      addTagForm($collectionHolder);

  });
}
}

  function addTagForm($collectionHolder) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    var newForm = prototype;
    // You need this only if you didn't set 'label' => false in your tags field in TaskType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__label__/g, index);

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = $('<div></div>').append(newForm);
    $collectionHolder.append($newFormLi);

}


