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
            'en': {
                    english:'English',
                    native:'English'
                },
            'enm': {
                    english:'English',
                    native:'English'
                },
            'eng': {
                    english:'English',
                    native:'English'
                },
            'es': {
                    english:'Spanish',
                    native:'Español'
                },
            'spa': {
                    english:'Spanish',
                    native:'Español'
                },
            'zh': {
                    english:'Chinese',
                    native:'中文'
                },
            'zho': {
                    english:'Chinese',
                    native:'中文'
                },
            'chi': {
                english:'Chinese',
                native:'中文'
            },
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
  
    function openTranslationsModalWithExpiry(modalId, lang) {
        if(getWithExpiry('phila-active-language') == null && $(modalId).length) {
            $(modalId).foundation('open');
  
            if ($('.reveal--announcement')[0]) {
  
                $('.reveal--announcement').on('closed.zf.reveal', function () {
                    $(modalId).foundation('open'); 
                });
            }
        } else if (getWithExpiry('phila-active-language') && lang != getWithExpiry('phila-active-language')) {
            $('#translate-'+lang.toLowerCase())[0].click();
        }
        if (lang.length) {
            $(modalId+' .button-text').click(function() {
                // one month expiry
                setWithExpiry('phila-active-language', lang, 2629800000);
            });
        }
    }
  
    // opens translations-modal if English isn't the detected local language
    function openTranslationsModal() {
        if (navigator.language && getWithExpiry('phila-active-language') === null) {
            let lang = philaLocaleCodeToEnglish(navigator.language);
            $('#translations-modal-lang').html(lang.english);
            $('#translate-page').click(function() {
                $('#translate-'+lang.english.toLowerCase())[0].click();
            });
            openTranslationsModalWithExpiry('#translations-modal', lang.english);
        }
    }
  
    $(document).ready(function() {
        openDisclaimerModal();
        openTranslationsModal();
    });
    
  });