
// Initialize your app
var myApp = new Framework7(
{
    material: true
});

// Export selectors engine
var $$ = Dom7;

// Add view
var mainView = myApp.addView('.view-main', {
    // Because we use fixed-through navbar we can enable dynamic navbar
    dynamicNavbar: true
});

myApp.onPageInit('signup', function (page) {
    myApp.alert("hey");
});


// Callbacks to run specific code for specific pages, for example for About page:
/*myApp.onPageInit('about', function (page) {
    // run createContentPage func after link was clicked
    $$('.create-page').on('click', function () {
        createContentPage();
    });
});
*/
