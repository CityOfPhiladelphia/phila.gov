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

    // opens disclaimer-modal if exists for department & program pages
    function openDisclaimerModal() {
        let modalSlug = window.location.pathname.split('/');

        if( ( modalSlug[1] == 'departments' || modalSlug[1] == 'programs') && modalSlug[2] ) {
            modalSlug = modalSlug.slice(1,3).join('-');
        }
        openModalWithExpiry('#disclaimer-modal', modalSlug);
    }

    function philaLocaleCodeToEnglish(loc) {
        if (typeof loc !== 'string') {
            loc = loc[0];
        }
        let parts = loc.split('-');
        let langs = {
            'en': 'English',
            'enm': 'English',
            'eng': 'English',
            'es': 'Spanish',
            'spa': 'Spanish',
            'zh': 'Chinese',
            'zho': 'Chinese',
            'chi': 'Chinese',
        }
        if (parts.length) {
            return langs[parts[0]];
        }
        return 'language';
    }

    function openModalWithExpiry(modalId, modalSlug) {
        if(getWithExpiry('phila-modal-'+modalSlug) == null && $(modalId).length) {
            $(modalId).foundation('open');

            if ($('.reveal--announcement')[0]) {

                $('.reveal--announcement').on('closed.zf.reveal', function () {
                    $(modalId).foundation('open'); 
                });
            }
        }
        $(modalId+' .button-text').click(function() {
            // two week expiry
            setWithExpiry('phila-modal-'+modalSlug, 'seen', 1209600000);
        });
    }

    // opens translations-modal if English isn't the detected local language
    function openTranslationsModal() {
        $('#translations-modal-lang').html(philaLocaleCodeToEnglish(navigator.language));
        openModalWithExpiry('#translations-modal-lang', 'translations');
    }

    $(document).ready(function() {
        openDisclaimerModal();
        openTranslationsModal();
    });
    
});