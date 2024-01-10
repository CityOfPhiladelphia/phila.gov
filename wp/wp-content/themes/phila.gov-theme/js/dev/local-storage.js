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
        switch (parts[0]) {
            case 'enm':
            case 'eng':
            case 'en':
                return {
                    english:'English',
                    native:'English'
                }
            case 'es':
            case 'spa':
                return {
                    english:'Spanish',
                    native:'Español'
                }
            case 'zh':
            case 'zho':
            case 'chi':
                return {
                    english:'Chinese',
                    native:'中文'
                }
            default:
                return {
                    english:'English',
                    native:'English'
                }
        }
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
            $(modalId+' #translate-page').click(function() {
                // one month expiry
                setWithExpiry('phila-active-language', lang, 2629800000);
            });
        }
    }
  
    // opens translations-modal if English isn't the detected local language
    function openTranslationsModal() {
        if (navigator.language) {
            let lang = philaLocaleCodeToEnglish(navigator.language);
            let localLang = getWithExpiry('phila-active-language');
            let currentUrl = window.location.pathname.split('/');
            let pathItem = currentUrl[1];
            let currentPageLang = philaLocaleCodeToEnglish(pathItem);
            if (localLang === null) {
            
                $('#translations-modal-lang').html(lang.english);
                $('#translate-page').click(function() {
                    $('#translate-'+lang.english.toLowerCase())[0].click();
                });
                if (currentPageLang && lang.english != currentPageLang.english) {
                    openTranslationsModalWithExpiry('#translations-modal', lang.english);
                }
            } else if (localLang && currentPageLang && localLang != currentPageLang.english) {
                $('#translate-'+localLang.toLowerCase())[0].click();
            }
        }
    }

    function setLangWithExpiry() {
        $(".translations-nav a").click(function(){
            let urlPath = $(this)[0].href.split('/');
            let pathItem = urlPath[3];
            let lang = philaLocaleCodeToEnglish(pathItem);
            if (lang) {
                setWithExpiry('phila-active-language', lang.english, 2629800000);
            }
        });
    }
  
    $(document).ready(function() {
        openDisclaimerModal();
        openTranslationsModal();
        setLangWithExpiry();
    });
    
  });