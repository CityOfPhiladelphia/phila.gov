module.exports = $(function(){
    
    function setWithExpiry(key, value, ttl) {
        const now = new Date()
        const item = {
            value: value,
            expiry: now.getTime() + ttl
        }
        localStorage.setItem(key, JSON.stringify(item))
    }

    function getWithExpiry(key) {
        const itemStr = localStorage.getItem(key)
        if (!itemStr) {
            return null
        }
        const item = JSON.parse(itemStr)
        const now = new Date()
        if (now.getTime() > item.expiry) {
            localStorage.removeItem(key)
            return null
        }
        return item.value
    }


    // opens cto-modal if exists for department & program pages
    $(document).ready(function() {
        let modalSlug = window.location.pathname.split('/');

        if( ( modalSlug[1] == 'departments' || modalSlug[1] == 'programs') && modalSlug[2] ) {
            modalSlug = modalSlug.slice(1,3).join('-');
        }

        if(getWithExpiry('phila-modal-'+modalSlug) == null && $('#cto-modal').length) {
            $('#cto-modal').foundation('open');

            // expires in 1 week or 604800 seconds
            setWithExpiry('phila-modal-'+modalSlug, 'seen', 604800);
        }
    });
    
});