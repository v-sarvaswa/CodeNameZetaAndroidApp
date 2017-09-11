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

// Callbacks to run specific code for specific pages, for example for About page:
myApp.onPageInit('about', function (page) {
    // run createContentPage func after link was clicked
    $$('.create-page').on('click', function () {
        createContentPage();
    });
});

// Generate dynamic page
var dynamicPageIndex = 0;
function createContentPage() {
	mainView.router.loadContent(
        '<!-- Top Navbar-->' +
        '<div class="navbar">' +
        '  <div class="navbar-inner">' +
        '    <div class="left"><a href="#" class="back link"><i class="icon icon-back"></i><span>Back</span></a></div>' +
        '    <div class="center sliding">Dynamic Page ' + (++dynamicPageIndex) + '</div>' +
        '  </div>' +
        '</div>' +
        '<div class="pages">' +
        '  <!-- Page, data-page contains page name-->' +
        '  <div data-page="dynamic-pages" class="page">' +
        '    <!-- Scrollable page content-->' +
        '    <div class="page-content">' +
        '      <div class="content-block">' +
        '        <div class="content-block-inner">' +
        '          <p>Here is a dynamic page created on ' + new Date() + ' !</p>' +
        '          <p>Go <a href="#" class="back">back</a> or go to <a href="services.html">Services</a>.</p>' +
        '        </div>' +
        '      </div>' +
        '    </div>' +
        '  </div>' +
        '</div>'
    );
	return;
}