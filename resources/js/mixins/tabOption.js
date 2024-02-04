const tabOption = {
    methods: {
        getTabIndex(tabs) {
            // Get the pathname from the URL
            const pathname = window.location.pathname;

            // Split the pathname into segments using "/" as the delimiter
            const segments = pathname.split("/");

            // The last segment should contain "1" in this case
            const desiredValue = segments[segments.length - 1];

            return tabs.indexOf(desiredValue);
        },

        updateUrl(tabs, activeIndex) {
            let currentURL = window.location.href;
            let lastSlashIndex = currentURL.lastIndexOf('/');
            if (lastSlashIndex !== -1) {
                let pathBeforeLastSlash = currentURL.substring(0, lastSlashIndex);
                let newURL = pathBeforeLastSlash + '/' + tabs[parseInt(activeIndex)];
                history.pushState({}, null, newURL);
            }
        }
    }
}

export default tabOption;
