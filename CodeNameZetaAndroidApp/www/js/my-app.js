// Initialize your app
var myApp = new Framework7();

// Export selectors engine
var $$ = Dom7;

// Add view
var mainView = myApp.addView('.view-main', {
    // Because we use fixed-through navbar we can enable dynamic navbar
    dynamicNavbar: true
});

myApp.onPageInit('notifications', function (page) {
    $$('.ks-notification-1').on('click', function () {
        myApp.addNotification({
            message: 'Simple message'
        });
    });
    $$('.ks-notification-2').on('click', function () {
        myApp.addNotification({
            message: 'Multi-line message. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc in magna nisi.',
        });
    });
    $$('.ks-notification-3').on('click', function () {
        myApp.addNotification({
            message: 'Nice yellow button',
            button: {
                text: 'Click me',
                color: 'yellow'
            }
        });
    });
    $$('.ks-notification-4').on('click', function () {
        myApp.addNotification({
            message: 'Close me to see Alert',
            button: {
                text: 'Close',
                color: 'lightgreen'
            },
            onClose: function () {
                myApp.alert('Notification closed');
            }
        });
    });
});

myApp.onPageInit('swiper-horizontal', function (page) {
    alert("hello");
});

myApp.onPageInit('signup_stage_2', function (page) {
    alert("hello");
});

// Callbacks to run specific code for specific pages, for example for About page:
myApp.onPageInit('about', function (page) {
    // run createContentPage func after link was clicked
    $$('.create-page').on('click', function () {
        createContentPage();
    });
});
