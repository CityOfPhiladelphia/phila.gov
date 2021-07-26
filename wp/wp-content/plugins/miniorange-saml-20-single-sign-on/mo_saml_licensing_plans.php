<?php
    
function mo_saml_show_licensing_page(){

    $supportmail = "samlsupport@xecurify.com";
    $current_user = wp_get_current_user();
    $fname = $current_user->user_firstname;
    $lname = $current_user->user_lastname;
    wp_enqueue_script('jquery');
    wp_enqueue_style( 'mo_saml_contact_us', plugins_url( 'includes/css/support.css', __FILE__ ),'','5.3' );
    ?>
    

    <?php
    echo '<style>.update-nag, .updated, .error, .is-dismissible, .notice, .notice-error { display: none; }</style>';
    ?>
    <style>
        *, *::after, *::before {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        html {
            font-size: 62.5%;
        }

        html * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .pricing-container {
            font-size: 1.6rem;
            font-family: "Open Sans", sans-serif;
            color: #fff;
        }

        /* --------------------------------

        Main Components

        -------------------------------- */
        .cd-header{
            margin-top:100px;
        }
        .cd-header>h1{
            text-align: center;
            color: #FFFFFF;
            font-size: 3.2rem;
        }

        .cd-pricing-container {
            width: 100%;
            max-width: 1460px;
            margin: 4em auto;
        }
        @media only screen and (min-width: 768px) {
            .cd-pricing-container {
                margin: auto;
            }
            .cd-pricing-container.cd-full-width {
                width: 100%;
                max-width: none;
            }
        }

        .cd-pricing-switcher {
            text-align: center;
        }
        .cd-pricing-switcher .fieldset {
            display: inline-block;
            position: relative;
            border-radius: 50em;
            border: 1px solid #e97d68;
        }
        .cd-pricing-switcher input[type="radio"] {
            position: absolute;
            opacity: 0;
        }
        .cd-pricing-switcher label {
            position: relative;
            z-index: 1;
            display: inline-block;
            float: left;
            width: 160px;
            height: 40px;
            line-height: 40px;
            cursor: pointer;
            font-size: 1.4rem;
            color: #FFFFFF;
            font-size:18px;
        }
        .cd-pricing-switcher .cd-switch {
            /* floating background */
            position: absolute;
            top: 2px;
            left: 2px;
            height: 40px;
            width: 160px;
            background-color: black;
            border-radius: 50em;
            -webkit-transition: -webkit-transform 0.5s;
            -moz-transition: -moz-transform 0.5s;
            transition: transform 0.5s;
        }
        .cd-pricing-switcher input[type="radio"]:checked + label + .cd-switch,
        .cd-pricing-switcher input[type="radio"]:checked + label:nth-of-type(n) + .cd-switch {
            /* use label:nth-of-type(n) to fix a bug on safari with multiple adjacent-sibling selectors*/
            -webkit-transform: translateX(155px);
            -moz-transform: translateX(155px);
            -ms-transform: translateX(155px);
            -o-transform: translateX(155px);
            transform: translateX(155px);
        }

        .no-js .cd-pricing-switcher {
            display: none;
        }

        .cd-pricing-list {
            margin: 2em 0 0;
        }
        .cd-pricing-list > li {
            position: relative;
            margin-bottom: 1em;
        }
        @media only screen and (min-width: 768px) {
            .cd-pricing-list {
                margin: 3em 0 0;
            }
            .cd-pricing-list:after {
                content: "";
                display: table;
                clear: both;
            }
            .cd-pricing-list > li {
                width: 35.3333333333%;
                float: left;
            }
            .cd-has-margins .cd-pricing-list > li {
                width:23.6%;
                float: left;
                margin-right: 1.5%;
            }
            .cd-has-margins .cd-pricing-list > li:last-of-type {
                margin-right: 0;
            }
        }

        .cd-pricing-wrapper {
            /* this is the item that rotates */
            overflow: show;
            position: relative;
        }



        .touch .cd-pricing-wrapper {
            /* fix a bug on IOS8 - rotating elements dissapear*/
            -webkit-perspective: 2000px;
            -moz-perspective: 2000px;
            perspective: 2000px;
        }
        .cd-pricing-wrapper.is-switched .is-visible {
            /* totate the tables - anticlockwise rotation */
            -webkit-transform: rotateY(180deg);
            -moz-transform: rotateY(180deg);
            -ms-transform: rotateY(180deg);
            -o-transform: rotateY(180deg);
            transform: rotateY(180deg);
            -webkit-animation: cd-rotate 0.5s;
            -moz-animation: cd-rotate 0.5s;
            animation: cd-rotate 0.5s;
        }
        .cd-pricing-wrapper.is-switched .is-hidden {
            /* totate the tables - anticlockwise rotation */
            -webkit-transform: rotateY(0);
            -moz-transform: rotateY(0);
            -ms-transform: rotateY(0);
            -o-transform: rotateY(0);
            transform: rotateY(0);
            -webkit-animation: cd-rotate-inverse 0.5s;
            -moz-animation: cd-rotate-inverse 0.5s;
            animation: cd-rotate-inverse 0.5s;
            opacity: 0;
        }
        .cd-pricing-wrapper.is-switched .is-selected {
            opacity: 1;
        }
        .cd-pricing-wrapper.is-switched.reverse-animation .is-visible {
            /* invert rotation direction - clockwise rotation */
            -webkit-transform: rotateY(-180deg);
            -moz-transform: rotateY(-180deg);
            -ms-transform: rotateY(-180deg);
            -o-transform: rotateY(-180deg);
            transform: rotateY(-180deg);
            -webkit-animation: cd-rotate-back 0.5s;
            -moz-animation: cd-rotate-back 0.5s;
            animation: cd-rotate-back 0.5s;
        }
        .cd-pricing-wrapper.is-switched.reverse-animation .is-hidden {
            /* invert rotation direction - clockwise rotation */
            -webkit-transform: rotateY(0);
            -moz-transform: rotateY(0);
            -ms-transform: rotateY(0);
            -o-transform: rotateY(0);
            transform: rotateY(0);
            -webkit-animation: cd-rotate-inverse-back 0.5s;
            -moz-animation: cd-rotate-inverse-back 0.5s;
            animation: cd-rotate-inverse-back 0.5s;
            opacity: 0;
        }
        .cd-pricing-wrapper.is-switched.reverse-animation .is-selected {
            opacity: 1;
        }
        .cd-pricing-wrapper > li {
            background-color: #FFFFFF;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            /* Firefox bug - 3D CSS transform, jagged edges */
            outline: 1px solid transparent;
        }
        .cd-pricing-wrapper > li::after {
            /* subtle gradient layer on the right - to indicate it's possible to scroll */
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            height: 100%;
            width: 50px;
            pointer-events: none;
            background: -webkit-linear-gradient( right , #FFFFFF, rgba(255, 255, 255, 0));
            background: linear-gradient(to left, #FFFFFF, rgba(255, 255, 255, 0));
        }
        .cd-pricing-wrapper > li.is-ended::after {
            /* class added in jQuery - remove the gradient layer when it's no longer possible to scroll */
            display: none;
        }
        .cd-pricing-wrapper .is-visible {
            /* the front item, visible by default */
            position: relative;
            background-color: #f2f5f8;
        }
        .cd-pricing-wrapper .is-hidden {
            /* the hidden items, right behind the front one */
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            z-index: 1;
            -webkit-transform: rotateY(180deg);
            -moz-transform: rotateY(180deg);
            -ms-transform: rotateY(180deg);
            -o-transform: rotateY(180deg);
            transform: rotateY(180deg);
        }
        .cd-pricing-wrapper .is-selected {
            /* the next item that will be visible */
            z-index: 3 !important;
        }
        @media only screen and (min-width: 768px) {
            .cd-pricing-wrapper > li::before {
                /* separator between pricing tables - visible when number of tables > 3 */
                content: '';
                position: absolute;
                z-index: 6;
                left: -1px;
                top: 50%;
                bottom: auto;
                -webkit-transform: translateY(-50%);
                -moz-transform: translateY(-50%);
                -ms-transform: translateY(-50%);
                -o-transform: translateY(-50%);
                transform: translateY(-50%);
                height: 50%;
                width: 1px;
                background-color: #b1d6e8;
            }
            .cd-pricing-wrapper > li::after {
                /* hide gradient layer */
                display: none;
            }
            .cd-popular .cd-pricing-wrapper > li {
                box-shadow: inset 0 0 0 3px #e97d68;
            }
            .cd-has-margins .cd-pricing-wrapper > li, .cd-has-margins .cd-popular .cd-pricing-wrapper > li {
                box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
            }
            .cd-secondary-theme .cd-pricing-wrapper > li {
                background: #3aa0d1;
                background: -webkit-linear-gradient( bottom , #3aa0d1, #3ad2d1);
                background: linear-gradient(to top, #3aa0d1, #3ad2d1);
            }
            .cd-secondary-theme .cd-popular .cd-pricing-wrapper > li {
                background: #e97d68;
                background: -webkit-linear-gradient( bottom , #e97d68, #e99b68);
                background: linear-gradient(to top, #e97d68, #e99b68);
                box-shadow: none;
            }
            :nth-of-type(1) > .cd-pricing-wrapper > li::before {
                /* hide table separator for the first table */
                display: none;
            }
            .cd-has-margins .cd-pricing-wrapper > li {
                border-radius: 4px 4px 6px 6px;
            }
            .cd-has-margins .cd-pricing-wrapper > li::before {
                display: none;
            }
        }
        @media only screen and (min-width: 1500px) {
            .cd-full-width .cd-pricing-wrapper > li {
                padding: 2.5em 0;
            }
        }

        .no-js .cd-pricing-wrapper .is-hidden {
            position: relative;
            -webkit-transform: rotateY(0);
            -moz-transform: rotateY(0);
            -ms-transform: rotateY(0);
            -o-transform: rotateY(0);
            transform: rotateY(0);
            margin-top: 1em;
        }

        @media only screen and (min-width: 768px) {
            .cd-popular .cd-pricing-wrapper > li::before {
                /* hide table separator for .cd-popular table */
                display: none;
            }

            .cd-popular + li .cd-pricing-wrapper > li::before {
                /* hide table separator for tables following .cd-popular table */
                display: none;
            }
        }
        .cd-pricing-header {
            position: relative;

            height: 80px;
            padding: 1em;
            pointer-events: none;
            background-color: #3aa0d1;
            color: #FFFFFF;
        }
        .cd-pricing-header h2 {
            margin-bottom: 3px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .cd-popular .cd-pricing-header {
            background-color: #e97d68;
        }
        @media only screen and (min-width: 768px) {
            .cd-pricing-header {
                height: 200px;
                padding: 1.9em 0.9em 1.6em;
                pointer-events: auto;
                text-align: center;
                color: #2f6062;
                background-color: transparent;
            }
            .cd-popular .cd-pricing-header {
                color: #e97d68;
                background-color: transparent;
            }
            .cd-secondary-theme .cd-pricing-header {
                color: #FFFFFF;
            }
            .cd-pricing-header h2 {
                font-size: 1.8rem;
                letter-spacing: 2px;
            }
        }

        .cd-currency, .cd-value {
            font-size: 4rem;
            font-weight: 300;
        }

        .cd-duration {
            font-weight: 800;
            font-size: 1.3rem;
            color: #8dc8e4;
            text-transform: uppercase;
        }
        .user-label {
            font-weight: 700;
            font-size: 1.3rem;
            color: #8dc8e4;
            text-transform: uppercase;
        }
        .cd-popular .cd-duration {
            color: #f3b6ab;
        }
        .cd-duration::before {
            content: '/';
            margin-right: 2px;
        }

        @media only screen and (min-width: 768px) {
            .cd-value {
                font-size: 4rem;
                font-weight: 300;
            }

            .cd-contact {
                font-size: 3rem;

            }

            .cd-currency, .cd-duration {
                color: rgba(23, 61, 80, 0.4);
            }
            .cd-popular .cd-currency, .cd-popular .cd-duration {
                color: #e97d68;
            }
            .cd-secondary-theme .cd-currency, .cd-secondary-theme .cd-duration {
                color: #2e80a7;
            }
            .cd-secondary-theme .cd-popular .cd-currency, .cd-secondary-theme .cd-popular .cd-duration {
                color: #ba6453;
            }

            .cd-currency {
                display: inline-block;
                margin-top: 10px;
                vertical-align: top;
                font-size: 2rem;
                font-weight: 700;
            }

            .cd-duration {
                font-size: 1.4rem;
            }
        }
        .cd-pricing-body {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .is-switched .cd-pricing-body {
            /* fix a bug on Chrome Android */
            overflow: hidden;
        }
        @media only screen and (min-width: 768px) {
            .cd-pricing-body {
                overflow-x: visible;
            }
        }

        .cd-pricing-features {
            width: 600px;
        }
        .cd-pricing-features:after {
            content: "";
            display: table;
            clear: both;
        }
        .cd-pricing-features li {
            width: 100px;
            float: left;
            padding: 1.6em 1em;
            font-size: 1.4rem;
            text-align: center;
            white-space: initial;

            line-height:1.4em;

            text-overflow: ellipsis;
            color: black;
            overflow-wrap: break-word;
            margin: 0 !important;

        }
        .cd-pricing-features em {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: black;
        }
        @media only screen and (min-width: 768px) {
            .cd-pricing-features {
                width: auto;
                word-wrap: break-word;
            }
            .cd-pricing-features li {
                float: none;
                width: auto;
                padding: 1em;
                word-wrap: break-word;
                font-size:1.3em;
            }
            .cd-popular .cd-pricing-features li {
                margin: 0 3px;
            }
            .cd-pricing-features li:nth-of-type(2n+1) {
                background-color: rgba(23, 61, 80, 0.06);
            }
            .cd-pricing-features em {
                display: inline-block;
                margin-bottom: 0;
                word-wrap: break-word;
            }
            .cd-has-margins .cd-popular .cd-pricing-features li, .cd-secondary-theme .cd-popular .cd-pricing-features li {
                margin: 0;
            }
            .cd-secondary-theme .cd-pricing-features li {
                color: #FFFFFF;
            }
            .cd-secondary-theme .cd-pricing-features li:nth-of-type(2n+1) {
                background-color: transparent;
            }
        }

        .cd-pricing-footer {
            position: absolute;
            z-index: 1;
            top: 0;
            left: 0;
            /* on mobile it covers the .cd-pricing-header */
            height: 80px;
            width: 100%;
        }
        .cd-pricing-footer::after {
            /* right arrow visible on mobile */
            content: '';
            position: absolute;
            right: 1em;
            top: 50%;
            bottom: auto;
            -webkit-transform: translateY(-50%);
            -moz-transform: translateY(-50%);
            -ms-transform: translateY(-50%);
            -o-transform: translateY(-50%);
            transform: translateY(-50%);
            height: 20px;
            width: 20px;
            background: url(../img/cd-icon-small-arrow.svg);
        }
        @media only screen and (min-width: 768px) {
            .cd-pricing-footer {
                position: relative;
                height: auto;
                padding: 1.8em 0;
                text-align: center;
            }
            .cd-pricing-footer::after {
                /* hide arrow */
                display: none;
            }
            .cd-has-margins .cd-pricing-footer {
                padding-bottom: 0;
            }
        }

        .cd-select {
            position: relative;
            z-index: 1;
            display: block;
            height: 100%;
            /* hide button text on mobile */
            overflow: hidden;
            text-indent: 100%;
            white-space: nowrap;
            color: transparent;
        }
        @media only screen and (min-width: 768px) {
            .cd-select {
                position: static;
                display: inline-block;
                height: auto;
                padding: 1.3em 3em;
                color: #FFFFFF;
                border-radius: 2px;
                background-color: #0c1f28;
                font-size: 1.4rem;
                text-indent: 0;
                text-transform: uppercase;
                letter-spacing: 2px;
            }
            .no-touch .cd-select:hover {
                background-color: #112e3c;
            }
            .cd-popular .cd-select {
                background-color: #e97d68;
            }
            .no-touch .cd-popular .cd-select:hover {
                background-color: #ec907e;
            }
            .cd-secondary-theme .cd-popular .cd-select {
                background-color: #0c1f28;
            }
            .no-touch .cd-secondary-theme .cd-popular .cd-select:hover {
                background-color: #112e3c;
            }
            .cd-has-margins .cd-select {
                display: block;
                padding: 1.7em 0;
                border-radius: 0 0 4px 4px;
            }
        }
        /* --------------------------------

        xkeyframes

        -------------------------------- */
        @-webkit-keyframes cd-rotate {
            0% {
                -webkit-transform: perspective(2000px) rotateY(0);
            }
            70% {
                /* this creates the bounce effect */
                -webkit-transform: perspective(2000px) rotateY(200deg);
            }
            100% {
                -webkit-transform: perspective(2000px) rotateY(180deg);
            }
        }
        @-moz-keyframes cd-rotate {
            0% {
                -moz-transform: perspective(2000px) rotateY(0);
            }
            70% {
                /* this creates the bounce effect */
                -moz-transform: perspective(2000px) rotateY(200deg);
            }
            100% {
                -moz-transform: perspective(2000px) rotateY(180deg);
            }
        }
        @keyframes cd-rotate {
            0% {
                -webkit-transform: perspective(2000px) rotateY(0);
                -moz-transform: perspective(2000px) rotateY(0);
                -ms-transform: perspective(2000px) rotateY(0);
                -o-transform: perspective(2000px) rotateY(0);
                transform: perspective(2000px) rotateY(0);
            }
            70% {
                /* this creates the bounce effect */
                -webkit-transform: perspective(2000px) rotateY(200deg);
                -moz-transform: perspective(2000px) rotateY(200deg);
                -ms-transform: perspective(2000px) rotateY(200deg);
                -o-transform: perspective(2000px) rotateY(200deg);
                transform: perspective(2000px) rotateY(200deg);
            }
            100% {
                -webkit-transform: perspective(2000px) rotateY(180deg);
                -moz-transform: perspective(2000px) rotateY(180deg);
                -ms-transform: perspective(2000px) rotateY(180deg);
                -o-transform: perspective(2000px) rotateY(180deg);
                transform: perspective(2000px) rotateY(180deg);
            }
        }
        @-webkit-keyframes cd-rotate-inverse {
            0% {
                -webkit-transform: perspective(2000px) rotateY(-180deg);
            }
            70% {
                /* this creates the bounce effect */
                -webkit-transform: perspective(2000px) rotateY(20deg);
            }
            100% {
                -webkit-transform: perspective(2000px) rotateY(0);
            }
        }
        @-moz-keyframes cd-rotate-inverse {
            0% {
                -moz-transform: perspective(2000px) rotateY(-180deg);
            }
            70% {
                /* this creates the bounce effect */
                -moz-transform: perspective(2000px) rotateY(20deg);
            }
            100% {
                -moz-transform: perspective(2000px) rotateY(0);
            }
        }
        @keyframes cd-rotate-inverse {
            0% {
                -webkit-transform: perspective(2000px) rotateY(-180deg);
                -moz-transform: perspective(2000px) rotateY(-180deg);
                -ms-transform: perspective(2000px) rotateY(-180deg);
                -o-transform: perspective(2000px) rotateY(-180deg);
                transform: perspective(2000px) rotateY(-180deg);
            }
            70% {
                /* this creates the bounce effect */
                -webkit-transform: perspective(2000px) rotateY(20deg);
                -moz-transform: perspective(2000px) rotateY(20deg);
                -ms-transform: perspective(2000px) rotateY(20deg);
                -o-transform: perspective(2000px) rotateY(20deg);
                transform: perspective(2000px) rotateY(20deg);
            }
            100% {
                -webkit-transform: perspective(2000px) rotateY(0);
                -moz-transform: perspective(2000px) rotateY(0);
                -ms-transform: perspective(2000px) rotateY(0);
                -o-transform: perspective(2000px) rotateY(0);
                transform: perspective(2000px) rotateY(0);
            }
        }
        @-webkit-keyframes cd-rotate-back {
            0% {
                -webkit-transform: perspective(2000px) rotateY(0);
            }
            70% {
                /* this creates the bounce effect */
                -webkit-transform: perspective(2000px) rotateY(-200deg);
            }
            100% {
                -webkit-transform: perspective(2000px) rotateY(-180deg);
            }
        }
        @-moz-keyframes cd-rotate-back {
            0% {
                -moz-transform: perspective(2000px) rotateY(0);
            }
            70% {
                /* this creates the bounce effect */
                -moz-transform: perspective(2000px) rotateY(-200deg);
            }
            100% {
                -moz-transform: perspective(2000px) rotateY(-180deg);
            }
        }
        @keyframes cd-rotate-back {
            0% {
                -webkit-transform: perspective(2000px) rotateY(0);
                -moz-transform: perspective(2000px) rotateY(0);
                -ms-transform: perspective(2000px) rotateY(0);
                -o-transform: perspective(2000px) rotateY(0);
                transform: perspective(2000px) rotateY(0);
            }
            70% {
                /* this creates the bounce effect */
                -webkit-transform: perspective(2000px) rotateY(-200deg);
                -moz-transform: perspective(2000px) rotateY(-200deg);
                -ms-transform: perspective(2000px) rotateY(-200deg);
                -o-transform: perspective(2000px) rotateY(-200deg);
                transform: perspective(2000px) rotateY(-200deg);
            }
            100% {
                -webkit-transform: perspective(2000px) rotateY(-180deg);
                -moz-transform: perspective(2000px) rotateY(-180deg);
                -ms-transform: perspective(2000px) rotateY(-180deg);
                -o-transform: perspective(2000px) rotateY(-180deg);
                transform: perspective(2000px) rotateY(-180deg);
            }
        }
        @-webkit-keyframes cd-rotate-inverse-back {
            0% {
                -webkit-transform: perspective(2000px) rotateY(180deg);
            }
            70% {
                /* this creates the bounce effect */
                -webkit-transform: perspective(2000px) rotateY(-20deg);
            }
            100% {
                -webkit-transform: perspective(2000px) rotateY(0);
            }
        }
        @-moz-keyframes cd-rotate-inverse-back {
            0% {
                -moz-transform: perspective(2000px) rotateY(180deg);
            }
            70% {
                /* this creates the bounce effect */
                -moz-transform: perspective(2000px) rotateY(-20deg);
            }
            100% {
                -moz-transform: perspective(2000px) rotateY(0);
            }
        }
        @keyframes cd-rotate-inverse-back {
            0% {
                -webkit-transform: perspective(2000px) rotateY(180deg);
                -moz-transform: perspective(2000px) rotateY(180deg);
                -ms-transform: perspective(2000px) rotateY(180deg);
                -o-transform: perspective(2000px) rotateY(180deg);
                transform: perspective(2000px) rotateY(180deg);
            }
            70% {
                /* this creates the bounce effect */
                -webkit-transform: perspective(2000px) rotateY(-20deg);
                -moz-transform: perspective(2000px) rotateY(-20deg);
                -ms-transform: perspective(2000px) rotateY(-20deg);
                -o-transform: perspective(2000px) rotateY(-20deg);
                transform: perspective(2000px) rotateY(-20deg);
            }
            100% {
                -webkit-transform: perspective(2000px) rotateY(0);
                -moz-transform: perspective(2000px) rotateY(0);
                -ms-transform: perspective(2000px) rotateY(0);
                -o-transform: perspective(2000px) rotateY(0);
                transform: perspective(2000px) rotateY(0);
            }
        }


        .tab-content {
            margin-left: 0%!important;
            margin-top: 0%!important;

        }
        .tab-content>.active {
            width: 100% !important;
        }

        .tab-pane,.cd-pricing-container,.cd-pricing-switcher ,.cd-row,.cd-row>div{

        }

        .center-pills { display: inline-block; }

        .nav-pills{
            border: 1px solid #fff;
            height:48px;
        }

        .nav-pills>li{
            width:250px;
        }

        .tab-font{
            vertical-align:text-bottom;
            font-size:20px;
        }

        .nav-pills>li+li {
            margin-left: 0px;
        }

        .nav-pills>li.active>a, .nav-pills>li.active>a:hover, .nav-pills>li.active>a:focus,.nav-pills>li.active>a:active{
            color: #1e3334;
            background-color:white;
            height:47px;
        }

        .nav-pills>li>a:hover {
            color:#fff;
            background: #E97D68;
            height:46px;
        }

        .nav-pills>li>a:focus{
            color:#fff;
            background:grey;
            height:47px;

        }

        .nav-pills>li.active{
            background-color: #fff;
        }

        .nav-pills>li>a {
            border-radius: 0px;
            height:47px;
            border-color:#E85700;
            font-weight: 500;
            color: #d3f3d3;
            text-transform:uppercase;
        }


        .ui-widget-content {
            border: 1px solid #bdc3c7;
            background: #e1e1e1;
            color: #222222;
            margin-top: 4px;
        }

        .ui-slider .ui-slider-handle {
            position: absolute !important;
            z-index: 2 !important;
            width: 3.2em !important;
            height: 2.2em !important;
            cursor: default !important;
            margin: 0 -20px auto !important;
            text-align: center !important;
            line-height: 30px !important;
            color: #FFFFFF !important;
            font-size: 15px !important;
        }




        .ui-state-default,
        .ui-widget-content .ui-state-default {
            background: #393a40 !important;
        }
        .ui-slider .ui-slider-handle {width:2em;left:-.6em;text-decoration:none;text-align:center;}
        .ui-slider-horizontal .ui-slider-handle {
            margin-left: -0.5em !important;
        }

        .ui-slider .ui-slider-handle {
            cursor: pointer;
        }

        .ui-slider a,
        .ui-slider a:focus {
            cursor: pointer;
            outline: none;
        }

        .price, .lead p {
            font-weight: 600;
            font-size: 32px;
            display: inline-block;
            line-height: 60px;
        }


        .price-slider {
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .price-form {
            background: #ffffff;
            margin-bottom: 10px;
            padding: 20px;
            border: 1px solid #eeeeee;
            border-radius: 4px;
        }



        .help-text {
            display: block;
            margin-top: 32px;
            margin-bottom: 10px;
            color: #737373;
            position: absolute;
            font-weight: 200;
            text-align: right;
            width: 188px;
        }

        .price-form label {
            font-weight: 200;
            font-size: 21px;
        }

        .ui-slider-range-min {
            background: #2980b9;
        }

        .ui-slider-label-inner {
            border-top: 10px solid #393a40;
            display: block;
            left: 50%;
            position: absolute;
            top: 10%;
            z-index: 99;
        }

        .ui-slider-horizontal .ui-slider-handle {
            top: -.6em !important;
        }
        /***********************ADDED BY SHAILESH************************/

        .plan-tagline{
            margin:1px;
            font-size: 2rem;
            font-weight: 400;
        }

        .pricing-tooltip {
            position: relative;
            display: inline-block;
            /* color:black; */
        }

        .tooltip {
            display:none;
            background: black;
            font-size:12px;
            height:10px;
            width:80px;
            padding:10px;
            color:#fff;
            z-index: 99;
            bottom: 10px;
            border: 2px solid white;
            /* for IE */
            filter:alpha(opacity=80);
            /* CSS3 standard */
            opacity:0.8;
        }
        .pricing-tooltip .pricing-tooltiptext {
            visibility: hidden;
            background-color: black;
            line-height: 1.5em;
            font-size:12px;
            min-width: 300px;
            color: rgb(253, 252, 252);
            padding: 10px;
            border-radius: 6px;
            position: absolute;
            z-index: 5;
            text-align: center;
        }

        .pricing-tooltiptext .body{
            font-weight:100;
        }

        .pricing-tooltip:hover .pricing-tooltiptext {
            visibility: visible;
        }

        .pricing-dotted-border{
            border-bottom: 1px dotted black;
        }
        .pricing-tooltip-class,.pricing-tooltip-class:hover{
            color:black;
            border-bottom: 1px dotted black;
        }
        .pricing-tooltip-class:focus{
            color:black;
            text-decoration: none;
        }

        .toggle-div{
            cursor: pointer;
            font-size:1.5em;
        }

        .toggler_more{
            font-size: 1.1em;
            font-weight: bold;

            cursor: pointer;
        }

        .cd-pricing-features>li>a{
            color:#E97D68;
        }

        .pc-header{
            font-size:18px;
        }

        .cd-row .col-md-4, .cd-row .col-md-6 {
            padding-left: 30px!important;
            font-size: 16px;
            padding: 4px;
        }

        .cd-row .col-md-6 {
            width: 60.33333333%;
        }


        .ribbon {
            font-size: 12px !important;
            /* This ribbon is based on a 16px font side and a 24px vertical rhythm. I've used em's to position each element for scalability. If you want to use a different font size you may have to play with the position of the ribbon elements */

            width: 8%;

            position: relative;
            background: #ba89b6;
            color: #fff;
            text-align: center;
            padding-top: 8px; /* Adjust to suit */
            padding-bottom: 8px;
            margin: 2em auto 3em; /* Based on 24px vertical rhythm. 48px bottom margin - normally 24 but the ribbon 'graphics' take up 24px themselves so we double it. */
        }
        .ribbon:before, .ribbon:after {
            content: "";
            position: absolute;
            display: block;
            bottom: -1em;
            border: 15px solid #986794;
            z-index: -1;
        }
        .ribbon:before {
            left: -2em;
            border-right-width: 1.5em;
            border-left-color: transparent;
        }
        .ribbon:after {
            right: -2em;
            border-left-width: 1.5em;
            border-right-color: transparent;
        }
        .ribbon .ribbon-content:before, .ribbon .ribbon-content:after {
            content: "";
            position: absolute;
            display: block;
            border-style: solid;
            border-color: #804f7c transparent transparent transparent;
            bottom: -1em;
        }
        .ribbon .ribbon-content:before {
            left: 0;
            border-width: 0em 0 0 1em;
        }
        .ribbon .ribbon-content:after {
            right: 0;
            border-width: 0em 1em 0 0;
        }
        .ribbon-placement-1{
            margin-left: -34%;
            position: relative;
            margin-bottom: -80px;
            z-index: 1;
        }

        .ribbon-placement-2{
            margin-left: 34%;
            position: relative;
            margin-bottom: -60px;
            z-index: 1;
        }

        .popover {
            max-width: 25%;
            width: 25%;
            border-radius: 5px;
        }
        .popover-header{ background: rgb(233, 125, 104); color: white;}
        h3.subheading_plans {
            font-size: 16px;
        }
        .bottom-heading{
            font-size: 16px; 
            font-weight: 600;
        }

    </style>

<?php
    $lp = get_option('mo_license_plan_from_feedback');

    $sssborder = 'none;';
    $sspborder = 'none;';
    $sseborder = 'none;';
    $mspborder = 'none;';
    $mseborder = 'none;';
    $msbborder = 'none;';
    $license_plan_selected = 'singlesite';
    if($lp == 'Single Site - Standard'){
        $sssborder = '8px solid red;';
    }else if($lp == 'Single Site - Premium'){
        $sspborder = '8px solid red;';
    }else if($lp == 'Single Site - Enterprise'){
        $sseborder = '8px solid red;';
    }else if($lp == 'Multisite Network - Premium'){
        $mspborder = '8px solid red;';
        $license_plan_selected = 'multisite';
    }else if($lp == 'Multisite Network - Enterprise'){
        $mseborder = '8px solid red;';
        $license_plan_selected = 'multisite';
    }else if($lp == 'Multisite Network - Business'){
        $msbborder = '8px solid red;';
        $license_plan_selected = 'multisite';
    }

?>
<div style="text-align: center; font-size: 14px; background: forestgreen; color: white; padding-top: 4px; padding-bottom: 4px; border-radius: 16px;"><?php echo get_option('mo_saml_license_message'); ?></div>
    <input type="hidden" id="mo_license_plan_selected" value="<?php echo $license_plan_selected; ?>" />
    <div class="tab-content">
    <div class="tab-pane active text-center" id="cloud">

        <div class="cd-pricing-container cd-has-margins"><br>
            <h1 style="font-size: 32px;"><?php _e('Choose Your Licensing Plan','miniorange-saml-20-single-sign-on');?></h1>
            <div class="cd-pricing-switcher">
                <p class="fieldset" style="background-color: #e97d68;">
                    <input type="radio" name="sitetype" value="singlesite" id="singlesite" checked>
                    <label for="singlesite"><?php _e('Single Site','miniorange-saml-20-single-sign-on'); ?></label>
                    <input type="radio" name="sitetype" value="multisite" id="multisite">
                    <label for="multisite"><?php _e('Multisite Network','miniorange-saml-20-single-sign-on');?></label>
                    <span class="cd-switch"></span>
                </p>
            </div>
            <div style="background: #F2F5FB;border-radius:5px;font-size: large;margin-top:10px;padding:10px;border-style: solid;border-color: #2f6062">
                    <span class="dashicons dashicons-info" style="vertical-align: bottom;"></span>
                    <?php _e('License is linked to the domain of the Wordpress instance, so if you have dev-staging-prod type of environment then you will require 3 licenses of the plugin (with discounts applicable on pre-production environments).','miniorange-saml-20-single-sign-on');
                    _e(' Contact us at <a style="color:blue" href="mailto:samlsupport@xecurify.com">samlsupport@xecurify.com</a> for bulk discounts.','miniorange-saml-20-single-sign-on');?>
                </div>
            <script>
                jQuery(document).ready(function(){
                    jQuery("#popover").popover({ trigger: "hover" });
                    jQuery("#popover1").popover({ trigger: "hover" });
                    jQuery("#popover2").popover({ trigger: "hover" });
                    jQuery("#popover3").popover({ trigger: "hover" });
                    jQuery("#popover4").popover({ trigger: "hover" });
                    jQuery("#popover5").popover({ trigger: "hover" });
                    jQuery("#popover6").popover({ trigger: "hover" });
                    jQuery("#popoverfree").popover({ trigger: "focus" });


                });
            </script>
            <!-- .cd-pricing-switcher -->




            <input type="hidden" value="<?php echo mo_saml_is_customer_registered_saml(false);?>" id="mo_customer_registered">
            <ul id="list-type" class="cd-pricing-list cd-bounce-invert" >
                <li>

                    <ul class="cd-pricing-wrapper">
                        <li data-type="singlesite" class="mosslp is-visible" style="border: <?php echo $sssborder; ?>">
                            <a id="popover" data-toggle="popover" title="<h3><?php _e('Why should I choose this plan?','miniorange-saml-20-single-sign-on');?></h3>" data-placement="top" data-html="true"
                               data-content="<p><?php _e('Choose this plan if you are looking for the features like ','miniorange-saml-20-single-sign-on');?>
                               <br /><b>
                               <?php _e('Auto-Redirect to IdP','miniorange-saml-20-single-sign-on');?></b><br /><b>
                               <?php _e('Basic Attribute Mapping (Username, Email, First Name, Last Name, Display Name)','miniorange-saml-20-single-sign-on');?></b><br /><span style=\'color:red;\'><b>
                               <?php _e('Note:','miniorange-saml-20-single-sign-on');?></b></span>
                               <?php _e('Single Logout & Role Mapping is not a part of this plan.','miniorange-saml-20-single-sign-on');?></p>">
                            <header class="cd-pricing-header">

                                <h2 style="margin-bottom: 10px" ><?php _e('Standard','miniorange-saml-20-single-sign-on');?><span style="font-size:0.5em"></span></h2>
                                <h3 class="subheading_plans" style="color:black;"><?php _e('Auto-Redirect to IdP','miniorange-saml-20-single-sign-on');?><br /><br /></h3>
                                <div class="cd-price" >
                                    <span class="cd-currency">$</span>
                                    <span class="cd-value">349*</span></span>

                                </div>

                            </header> <!-- .cd-pricing-header -->
                            </a>
                            <footer class="cd-pricing-footer">
                                <a href="#" class="cd-select" onclick="upgradeform('wp_saml_sso_standard_plan')" ><?php _e('Upgrade Now','miniorange-saml-20-single-sign-on');?></a>
                            </footer>
                            <b style="color: coral;"><?php _e('See the Standard Plugin features list below','miniorange-saml-20-single-sign-on');?></b>
                            <div class="cd-pricing-body">
                                <ul class="cd-pricing-features">
                                    <li><?php _e('Unlimited Authentications','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Basic Attribute Mapping (Username, Email, First Name, Last Name,Display Name)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Widget, Shortcode to add IdP Login Link on your site','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Step-by-step guide to setup IdP','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Auto-Redirect to IdP from login page','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Protect your complete site (Auto-Redirect to IdP from any page)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Change SP base Url and SP Entity ID','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Options to select SAML Request binding type','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Integrated Windows Authentication (supported with AD FS)','miniorange-saml-20-single-sign-on');?></li>
                                    <li>&nbsp;</li>
                                    <li>&nbsp;</li>
                                    <li>&nbsp;</li>
                                    <li>&nbsp;<br />&nbsp;</li>
                                    <li>&nbsp;</li>
                                    <li>&nbsp;</li>
                                    <li>&nbsp;</li>
                                    <li>&nbsp;<br />&nbsp;<br />&nbsp;</li>
                                    <li>&nbsp;</li>
                                    <li>&nbsp;</li>
                                    <li>&nbsp;<b><?php _e('Add-Ons','miniorange-saml-20-single-sign-on');?></b><br /><?php _e('Purchase Separately','miniorange-saml-20-single-sign-on');?><br /><a style="color:blue;" target="_blank" href="https://www.miniorange.com/contact"><b><?php _e('Contact us','miniorange-saml-20-single-sign-on');?></b></a><br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;</li>

                                </ul>
                            </div> <!-- .cd-pricing-body -->
                        </li>

                        <li data-type="multisite" class="momslp is-hidden" style="border: <?php echo $mspborder; ?>">
                            <a id="popover3" data-toggle="popover" title="<h3><?php _e('Why should I choose this plan?','miniorange-saml-20-single-sign-on');?></h3>" data-placement="top" data-html="true"
                               data-content="<p><?php _e('Choose this plan if you have Multisite Network Installation and are looking for the features like ','miniorange-saml-20-single-sign-on');?><br /><b>
                               <?php _e('Subsite Specific SSO','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('Auto-Redirect to IdP','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('Advance Attribute Mapping','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('Role Mapping','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('IdP metadata sync','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('Support of custom SP and IdP certificate','miniorange-saml-20-single-sign-on');?><br /></b><span style=\'color:red;\'><b><?php _e('Note:','miniorange-saml-20-single-sign-on');?></b></span> <?php _e('Add-ons are not a part of this plan.','miniorange-saml-20-single-sign-on');?></p>">
                            <header class="cd-pricing-header">

                                <h2 style="margin-bottom: 10px" ><?php _e('Premium','miniorange-saml-20-single-sign-on');?><span style="font-size:0.5em"></span></h2>
                                <h3 class="subheading_plans" style="color:black;"><?php _e('Auto-Redirect to IdP','miniorange-saml-20-single-sign-on');?><br><?php _e('Attribute and Role Management','miniorange-saml-20-single-sign-on');?><br><?php _e('Connect all subsites to same IdP','miniorange-saml-20-single-sign-on');?><br><?php _e('Single Logout','miniorange-saml-20-single-sign-on');?></h3>
                                <div class="cd-price" >
                                    <span class="cd-currency">$</span>
                                    <span class="cd-value">449*</span></span>

                                </div>

                            </header>
                            </a>
                            <!-- .cd-pricing-header -->
                            <footer class="cd-pricing-footer">
                                <a href="#" class="cd-select" onclick="upgradeform('wp_saml_sso_multisite_basic_plan')" ><?php _e('Upgrade Now','miniorange-saml-20-single-sign-on');?></a>
                            </footer>
                            <b style="color: coral;"><?php _e('See the Multisite Premium Plugin features list below','miniorange-saml-20-single-sign-on');?></b>
                            <div class="cd-pricing-body">

                                <ul class="cd-pricing-features">
                                    <li><?php _e('Unlimited Authentications','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Basic Attribute Mapping (Username, Email, First Name, Last Name,Display Name)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Widget,Shortcode to add IdP Login Link on your site','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Step-by-step guide to setup IdP','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Auto-Redirect to IdP from login page','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Protect your complete site (Auto-Redirect to IdP from any page)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Change SP base Url and SP Entity ID','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Options to select SAML Request binding type','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('SAML Single Logout','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Integrated Windows Authentication (supported with AD FS)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Customized Role Mapping','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Auto-sync IdP Configuration from metadata','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Custom Attribute Mapping (Any attribute which is stored in user-meta table)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Store Multiple IdP Certificates','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Custom SP Certificate','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Multi-Site Support','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Sub-site specific SSO for Multisite','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Auto-Redirection from specific subsites','miniorange-saml-20-single-sign-on');?></li>
                                    <li>&nbsp;</li>
                                    <li>&nbsp;</li>
                                    <li>&nbsp;<br />&nbsp;</li>
                                    <li>&nbsp;</li>
                                    <li>&nbsp;</li>
                                    <li>&nbsp;<b><?php _e('Add-Ons','miniorange-saml-20-single-sign-on');?></b><br /><?php _e('Purchase Separately','miniorange-saml-20-single-sign-on');?><br /><a style="color:blue;" target="_blank" href="https://www.miniorange.com/contact"><b><?php _e('Contact us','miniorange-saml-20-single-sign-on');?></b></a><br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;</li>
                                    
                                </ul>
                            </div> <!-- .cd-pricing-body -->
                        </li>
                    </ul> <!-- .cd-pricing-wrapper -->
                </li>

                <li class="cd-popular">
                    <ul class="cd-pricing-wrapper">
                        <li data-type="singlesite" class="mosslp is-visible" style="border: <?php echo $sspborder; ?>">
                            <a id="popover1" data-toggle="popover" title="<h3><?php _e('Why should I choose this plan?','miniorange-saml-20-single-sign-on');?></h3>" data-placement="top" data-html="true"
                               data-content="<p><?php _e('Choose this plan if you are looking for the features like ','miniorange-saml-20-single-sign-on');?><br /><b>
                               <?php _e('Advance Attribute Mapping','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('Role Mapping','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('Single Logout','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('IdP metadata sync','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('Support of custom SP and IdP certificate','miniorange-saml-20-single-sign-on');?><br /></b><span style=\'color:red;\'>
                               <b><?php _e('Note:','miniorange-saml-20-single-sign-on');?></b></span>
                                <?php _e('Add-ons are not a part of this plan. All features of Standard Plan are included here.','miniorange-saml-20-single-sign-on');?></p>">
                            <header class="cd-pricing-header">

                                <h2 style="margin-bottom: 10px"><?php _e('Premium','miniorange-saml-20-single-sign-on');?></h2>
                                <h3 class="subheading_plans" style="color:black"><?php _e('Attribute & Role Management','miniorange-saml-20-single-sign-on');?><br><?php _e('Single Logout','miniorange-saml-20-single-sign-on');?><br /><br /></h3>

                                <div class="cd-price" >
                                    <span class="cd-currency">$</span>
                                    <span class="cd-value">449*</span></span>

                                </div>


                            </header> <!-- .cd-pricing-header -->
                            </a>
                            <footer class="cd-pricing-footer">
                                <a href="#" class="cd-select" onclick="upgradeform('wp_saml_sso_basic_plan')" ><?php _e('Upgrade Now','miniorange-saml-20-single-sign-on');?></a>
                            </footer>
                            <b><?php _e('See the Premium Plugin features list below','miniorange-saml-20-single-sign-on');?></b>
                            <div class="cd-pricing-body">
                                <ul class="cd-pricing-features">
                                    <li><?php _e('Unlimited Authentications','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Basic Attribute Mapping (Username, Email, First Name, Last Name,Display Name)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Widget,Shortcode to add IdP Login Link on your site','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Step-by-step guide to setup IdP','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Auto-Redirect to IdP from login page','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Protect your complete site (Auto-Redirect to IdP from any page)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Change SP base Url and SP Entity ID','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Options to select SAML Request binding type','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Integrated Windows Authentication (supported with AD FS)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('SAML Single Logout','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Customized Role Mapping','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Auto-sync IdP Configuration from metadata','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Custom Attribute Mapping (Any attribute which is stored in user-meta table)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Store Multiple IdP Certificates','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Custom SP Certificate','miniorange-saml-20-single-sign-on');?></li>
                                    <li>&nbsp;</li>
                                    <li>&nbsp;<br />&nbsp;<br />&nbsp;</li>
                                    <li>&nbsp;</li>
                                    <li>&nbsp;</li>
                                    <li>&nbsp;<b><?php _e('Add-Ons','miniorange-saml-20-single-sign-on');?></b><br /><?php _e('Purchase Separately','miniorange-saml-20-single-sign-on');?><br /><a style="color:blue;" target="_blank" href="https://www.miniorange.com/contact"><b><?php _e('Contact us','miniorange-saml-20-single-sign-on');?></b></a><br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;</li>
                                </ul>
                            </div> <!-- .cd-pricing-body -->
                        </li>

                        <li data-type="multisite" class="momslp is-hidden" style="border: <?php echo $mseborder; ?>">
                            <a id="popover4" data-toggle="popover" title="<h3><?php _e('Why should I choose this plan?','miniorange-saml-20-single-sign-on');?></h3>" data-placement="top" data-html="true"
                               data-content="<p><?php _e('Choose this plan if you have Multisite Network installation and are looking for features like ','miniorange-saml-20-single-sign-on');?><br /><b>
                               <?php _e('Mu Domain Mapping Support','miniorange-saml-20-single-sign-on');?><br>
                               <?php _e('Easy migration from staging to prod','miniorange-saml-20-single-sign-on');?><br>
                               <?php _e('Setup SSO with multiple IdPs','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('Subsite Specific SSO','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('Auto-Redirect to IdP','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('Advance Attribute Mapping','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('Role Mapping','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('IdP metadata sync','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('Support of custom SP and IdP certificate','miniorange-saml-20-single-sign-on');?></b></p>">
                            <header class="cd-pricing-header">

                                <h2 style="margin-bottom: 10px"><?php _e('Enterprise','miniorange-saml-20-single-sign-on');?></h2>

                                <h3 class="subheading_plans" style="color:black;"><?php _e('Mu Domain Mapping Support','miniorange-saml-20-single-sign-on');?><br><?php _e('Easy migration from staging to prod','miniorange-saml-20-single-sign-on');?><br><?php _e('Setup SSO with multiple IdPs','miniorange-saml-20-single-sign-on');?></h3>
                                <div class="cd-price" >
                                    <span class="cd-currency">$</span>
                                    <span class="cd-value">549*</span></span>

                                </div>


                            </header> <!-- .cd-pricing-header -->
                            </a>
                            <footer class="cd-pricing-footer">
                                <a href="#" class="cd-select" onclick="upgradeform('wp_saml_sso_multisite_multiple_idp_plan')" ><?php _e('Upgrade Now','miniorange-saml-20-single-sign-on');?></a>
                            </footer>
                            <b><?php _e('See the Multisite Enterprise Plugin features list below','miniorange-saml-20-single-sign-on');?></b>
                            <div class="cd-pricing-body">
                                <ul class="cd-pricing-features">
                                    <li><?php _e('Unlimited Authentications','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Basic Attribute Mapping (Username, Email, First Name, Last Name,Display Name)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Widget,Shortcode to add IdP Login Link on your site','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Step-by-step guide to setup IdP','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Auto-Redirect to IdP from login page','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Protect your complete site (Auto-Redirect to IdP from any page)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Change SP base Url and SP Entity ID','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Options to select SAML Request binding type','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('SAML Single Logout','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Integrated Windows Authentication (supported with AD FS)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Customized Role Mapping','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Auto-sync IdP Configuration from metadata','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Custom Attribute Mapping (Any attribute which is stored in user-meta table)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Store Multiple IdP Certificates','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Custom SP Certificate','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Multi-Site Support','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Sub-site specific SSO for Multisite','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Auto-Redirection from specific subsites','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Mu Domain Mapping Support','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Multiple IdP Support','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Compatible with multiple environments in a hosting provider like Pantheon, WP-Engine, Wordpress VIP','miniorange-saml-20-single-sign-on');?></li>
                                    <li>&nbsp;</li>
                                    <li>&nbsp;</li>
                                    <li>&nbsp;<b><?php _e('Add-Ons','miniorange-saml-20-single-sign-on');?></b><br/> <?php _e('SSO Login Audit','miniorange-saml-20-single-sign-on');?><br /><?php _e('Purchase Separately (Remaining)','miniorange-saml-20-single-sign-on');?><br /><a style="color:blue;" target="_blank" href="https://www.miniorange.com/contact"><b><?php _e('Contact us','miniorange-saml-20-single-sign-on');?></b></a>&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;</li>
                                    

                                </ul>
                            </div> <!-- .cd-pricing-body -->
                        </li>

                    </ul> <!-- .cd-pricing-wrapper -->
                </li>

                <li>
                    <ul class="cd-pricing-wrapper">
                        <li data-type="singlesite" class="mosslp is-visible" style="border: <?php echo $sseborder; ?>">
                            <a id="popover2" data-toggle="popover" title="<h3><?php _e('Why should I choose this plan?','miniorange-saml-20-single-sign-on');?></h3>" data-placement="top" data-html="true"
                               data-content="<p><?php _e('Choose this plan if you are looking for features like ','miniorange-saml-20-single-sign-on');?><br /><b>
                               <?php _e('Easy migration from dev to prod','miniorange-saml-20-single-sign-on');?><br/>
                               <?php _e('Support of multiple IdPs','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('SSO Login Audit','miniorange-saml-20-single-sign-on');?></br>
                               <?php _e('Advance Attribute Mapping','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('Role Mapping','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('IdP metadata sync','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('Support of custom SP and IdP certificate','miniorange-saml-20-single-sign-on');?></b><br /><span style=\'color:red;\'>
                               <b><?php _e('Note:','miniorange-saml-20-single-sign-on');?></b></span> <?php _e('Add-ons are not a part of this plan. All features of Premium Plan are included here.','miniorange-saml-20-single-sign-on');?></p>">
                            <header class="cd-pricing-header">
                                <h2 style="margin-bottom:10px;"><?php _e('Enterprise','miniorange-saml-20-single-sign-on');?></h2>
                                <h3 class="subheading_plans" style="color:black;"><?php _e('Easy migration from dev to prod','miniorange-saml-20-single-sign-on');?><br><?php _e('Multiple IdP Support','miniorange-saml-20-single-sign-on');?><br><?php _e('SSO Login Audit','miniorange-saml-20-single-sign-on');?><br /></h3>
                                <div class="cd-price" >
                                    <span class="cd-currency">$</span>
                                    <span class="cd-value">549*</span></span>

                                </div>
                            </header> <!-- .cd-pricing-header -->
                            </a>
                            <footer class="cd-pricing-footer">
                                <a href="#" class="cd-select" onclick="upgradeform('wp_saml_sso_multiple_idp_plan')" ><?php _e('Upgrade Now','miniorange-saml-20-single-sign-on');?></a>
                            </footer>
                            <b style="color: coral;"><?php _e('See the Enterprise Plugin features list below','miniorange-saml-20-single-sign-on');?></b>
                            <div class="cd-pricing-body">
                                <ul class="cd-pricing-features ">
                                    <li><?php _e('Unlimited Authentications','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Basic Attribute Mapping (Username, Email, First Name, Last Name,Display Name)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Widget,Shortcode to add IdP Login Link on your site','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Step-by-step guide to setup IdP','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Auto-Redirect to IdP from login page','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Protect your complete site (Auto-Redirect to IdP from any page)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Change SP base Url and SP Entity ID','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Options to select SAML Request binding type','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Integrated Windows Authentication (supported with AD FS)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('SAML Single Logout','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Customized Role Mapping','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Auto-sync IdP Configuration from metadata','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Custom Attribute Mapping (Any attribute which is stored in user-meta table)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Store Multiple IdP Certificates','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Custom SP Certificate','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Multiple IdP Support','miniorange-saml-20-single-sign-on');?></li>
                                    <li style="height:80px"><?php _e('Compatible with multiple environments in a hosting provider like Pantheon, WP-Engine, Wordpress VIP','miniorange-saml-20-single-sign-on');?></li>
                                    <li>&nbsp;</li>
                                    <li>&nbsp;</li>
                                    <li>&nbsp;<b><?php _e('Add-Ons','miniorange-saml-20-single-sign-on');?></b><br/> <?php _e('SSO Login Audit','miniorange-saml-20-single-sign-on');?><br /><?php _e('Purchase Separately (Remaining)','miniorange-saml-20-single-sign-on');?><br /><a style="color:blue;" target="_blank" href="https://www.miniorange.com/contact"><b><?php _e('Contact us','miniorange-saml-20-single-sign-on');?></b></a><br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;</li>

                                </ul>
                            </div> <!-- .cd-pricing-body -->

                        </li>

                        <li data-type="multisite" class="momslp is-hidden" style="border: <?php echo $msbborder; ?>">
                            <a id="popover5" data-toggle="popover" title="<h3><?php _e('Why should I choose this plan?','miniorange-saml-20-single-sign-on');?></h3>" data-placement="top" data-html="true"
                               data-content="<p><?php _e('Choose this plan if you are looking for ','miniorange-saml-20-single-sign-on');?><br /><b>
                               <?php _e('All exclusive features included','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('Add-ons (Page Restriction, BuddyPress Attribute Mapping, LearnDash Attribute Mapping, Media Restriction, Attribute based Redirection, Federation SSO, SCIM-User Provisioning, SSO Session Management, SSO Login Audit, Anonymous Login).','miniorange-saml-20-single-sign-on');?></b><br /><span style='color:red;'><b><?php _e('Note','miniorange-saml-20-single-sign-on');?>:</b></span> <?php _e('All the Add-ons are packaged with this plan. All features of all the Multisite Plans are included here.','miniorange-saml-20-single-sign-on');?></p>">
                            <header class="cd-pricing-header">
                                <h2 style="margin-bottom:10px;"><?php _e('All-Inclusive','miniorange-saml-20-single-sign-on');?></h2>
                                <h3 class="subheading_plans" style="color:black;"><?php _e('Multisite Network SSO with all features and all the Add-ons','miniorange-saml-20-single-sign-on');?><br /><br /></h3>
                                <div class="cd-price" >
                                    <span class="cd-currency">$</span>
                                    <span class="cd-value">649*</span>

                                </div>
                            </header> <!-- .cd-pricing-header -->
                            </a>
                            <footer class="cd-pricing-footer">
                                <a href="#" class="cd-select" onclick="upgradeform('wp_saml_sso_multisite_all_inclusive_plan')" ><?php _e('Upgrade Now','miniorange-saml-20-single-sign-on');?></a>
                            </footer>
                            <b style="color: coral;"><?php _e('See the multisite All-Inclusive Plan features list below','miniorange-saml-20-single-sign-on');?></b>
                            <div class="cd-pricing-body">
                                <ul class="cd-pricing-features">
                                    <li><?php _e('Unlimited Authentications','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Basic Attribute Mapping (Username, Email, First Name, Last Name,Display Name)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Widget,Shortcode to add IdP Login Link on your site','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Step-by-step guide to setup IdP','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Auto-Redirect to IdP from login page','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Protect your complete site (Auto-Redirect to IdP from any page)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Change SP base Url and SP Entity ID','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Options to select SAML Request binding type','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('SAML Single Logout','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Integrated Windows Authentication (supported with AD FS)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Customized Role Mapping','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Auto-sync IdP Configuration from metadata','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Custom Attribute Mapping (Any attribute which is stored in user-meta table)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Store Multiple IdP Certificates','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Custom SP Certificate','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Multi-Site Support','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Sub-site specific SSO for Multisite','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Auto-Redirection from specific subsites','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Mu Domain Mapping Support','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Multiple IdP Support','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Compatible with multiple environments in a hosting provider like Pantheon, WP-Engine, Wordpress VIP','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Customize the metadata contact information','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Configuring plugin using APIs','miniorange-saml-20-single-sign-on');?></li>
                                    <li><b><?php _e('Add-Ons','miniorange-saml-20-single-sign-on');?></b><br />
                                        1. <?php _e('Page Restriction Add-On','miniorange-saml-20-single-sign-on');?><br />
                                        2. <?php _e('Buddypress Attribute Mapping Add-On','miniorange-saml-20-single-sign-on');?><br />
                                        3. <?php _e('LearnDash Attribute Integration Add-On','miniorange-saml-20-single-sign-on');?><br />
                                        4. <?php _e('Media Restriction Add-On','miniorange-saml-20-single-sign-on');?><br/>
                                        5. <?php _e('Attribute based Redirection','miniorange-saml-20-single-sign-on');?><br/>
                                        6. <?php _e('Federation SSO Add-On','miniorange-saml-20-single-sign-on');?><br/>
                                        7. <?php _e('SCIM-User Provisioning','miniorange-saml-20-single-sign-on');?><br/>
                                        8. <?php _e('SSO Session Management','miniorange-saml-20-single-sign-on');?><br/>
                                        9. <?php _e('SSO Login Audit','miniorange-saml-20-single-sign-on');?><br/>
                                       10. <?php _e('Anonymous Login','miniorange-saml-20-single-sign-on');?><br/>
                                    
                                        </li>
                                    
                                </ul>
                            </div> <!-- .cd-pricing-body -->
                        </li>
                    </ul> <!-- .cd-pricing-wrapper -->
                </li>

                <li class="cd-popular">
                    <ul class="cd-pricing-wrapper">
                        <li data-type="singlesite" class="mosslp is-visible" style="border: <?php echo $sseborder; ?>">
                            <a id="popover6" data-toggle="popover" title="<h3><?php _e('Why should I choose this plan?','miniorange-saml-20-single-sign-on');?></h3>" data-placement="top" data-html="true"
                               data-content="<p><?php _e('Choose this plan if you are looking for ','miniorange-saml-20-single-sign-on');?><br /><b>
                               <?php _e('All exclusive features included','miniorange-saml-20-single-sign-on');?><br />
                               <?php _e('Add-ons (Page Restriction, BuddyPress Attribute Mapping, LearnDash Attribute Mapping, Media Restriction, Attribute based Redirection, Federation SSO, SCIM-User Provisioning, SSO Session Management, SSO Login Audit, Anonymous Login).','miniorange-saml-20-single-sign-on');?></b><br /><span style=\'color:red;\'>
                               <b><?php _e('Note:','miniorange-saml-20-single-sign-on');?></b></span>
                                <?php _e('All the Add-ons are packaged with this plan. All features of all the Plans are included here.','miniorange-saml-20-single-sign-on');?></p>">
                            <header class="cd-pricing-header">
                                <h2 style="margin-bottom:10px;"><?php _e('All-Inclusive','miniorange-saml-20-single-sign-on');?></h2>
                                <h3 class="subheading_plans" style="color:black;"><?php _e('All features along <br>with all Add-ons','miniorange-saml-20-single-sign-on');?><br /><br /></h3>
                                <div class="cd-price" >
                                    <span class="cd-currency">$</span>
                                    <span class="cd-value">649*</span></span>

                                </div>
                            </header> <!-- .cd-pricing-header -->
                            </a>
                            <footer class="cd-pricing-footer">
                                <a href="#" class="cd-select" onclick="upgradeform('wp_saml_sso_all_inclusive_plan')" ><?php _e('Upgrade Now','miniorange-saml-20-single-sign-on');?></a>
                            </footer>
                            <b ><?php _e('See the All-inclusive Plugin features list below','miniorange-saml-20-single-sign-on');?></b>
                            <div class="cd-pricing-body">
                                <ul class="cd-pricing-features ">
                                    <li><?php _e('Unlimited Authentications','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Basic Attribute Mapping (Username, Email, First Name, Last Name,Display Name)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Widget,Shortcode to add IdP Login Link on your site','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Step-by-step guide to setup IdP','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Auto-Redirect to IdP from login page','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Protect your complete site (Auto-Redirect to IdP from any page)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Change SP base Url and SP Entity ID','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Options to select SAML Request binding type','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Integrated Windows Authentication (supported with AD FS)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('SAML Single Logout','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Customized Role Mapping','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Auto-sync IdP Configuration from metadata','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Custom Attribute Mapping (Any attribute which is stored in user-meta table)','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Store Multiple IdP Certificates','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Custom SP Certificate','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Multiple IdP Support','miniorange-saml-20-single-sign-on');?></li>
                                    <li style="height:80px"><?php _e('Compatible with multiple environments in a hosting provider like Pantheon, WP-Engine, Wordpress VIP','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Customize the metadata contact information','miniorange-saml-20-single-sign-on');?></li>
                                    <li><?php _e('Configuring plugin using APIs','miniorange-saml-20-single-sign-on');?></li>
                                    <li><b><?php _e('Add-Ons','miniorange-saml-20-single-sign-on');?> </b><br />
                                        1. <?php _e('Page Restriction Add-On','miniorange-saml-20-single-sign-on');?><br />
                                        2. <?php _e('Buddypress Attribute Mapping Add-On','miniorange-saml-20-single-sign-on');?><br />
                                        3. <?php _e('LearnDash Attribute Integration Add-On','miniorange-saml-20-single-sign-on');?><br />
                                        4. <?php _e('Media Restriction Add-On','miniorange-saml-20-single-sign-on');?><br/>
                                        5. <?php _e('Attribute based Redirection','miniorange-saml-20-single-sign-on');?><br/>
                                        6. <?php _e('Federation SSO Add-On','miniorange-saml-20-single-sign-on');?><br/>
                                        7. <?php _e('SCIM-User Provisioning','miniorange-saml-20-single-sign-on');?><br/>
                                        8. <?php _e('SSO Session Management','miniorange-saml-20-single-sign-on');?><br/>
                                        9. <?php _e('SSO Login Audit','miniorange-saml-20-single-sign-on');?><br/>
                                       10. <?php _e('Anonymous Login','miniorange-saml-20-single-sign-on');?><br/>
                                    </li>

                                </ul>
                            </div> <!-- .cd-pricing-body -->

                        </li>

                       
                    </ul> <!-- .cd-pricing-wrapper -->
                </li>

                

            </ul> <!-- .cd-pricing-list -->
            
        </div> <!-- .cd-pricing-container -->
        <div style="text-align:left; font-size:12px;  padding-right:30px;">
            <h3><?php _e('Steps to Upgrade to Premium Plugin','miniorange-saml-20-single-sign-on');?> -</h3>
            <p><?php esc_html_e('1. Click on \'Upgrade now\' button of the required licensing plan. You will be redirected to miniOrange Login Console. Enter your password with which you created an account
                with us. After that you will be redirected to payment page.','miniorange-saml-20-single-sign-on');?></p>
            <p><?php _e('2. Enter your card details and complete the payment. On successful payment completion, you will see the link
                to download the premium plugin.','miniorange-saml-20-single-sign-on');?></p>
            <p><?php _e('3. To install the premium plugin, first deactivate and delete the free version of the plugin. Enable the "Keep Configuration Intact" checkbox before deactivating and deleting the plugin. By doing so, your saved configurations of the plugin will not get lost.','miniorange-saml-20-single-sign-on');?>

            <p><?php _e('4. From this point on, do not update the premium plugin from the Wordpress store.','miniorange-saml-20-single-sign-on');?></p>

            <h3>* <?php _e('Cost applicable for one instance only. Licenses are perpetual and the Support Plan includes 12 months of maintenance (support and version updates). You can renew maintenance after 12 months at 50% of the current license cost.','miniorange-saml-20-single-sign-on');?></h3>
            <br />
            <li class="bottom-heading"> <?php _e('MultiSite Network Support','miniorange-saml-20-single-sign-on');?> -<br></li>
            <p style="padding-left:14px"><b>*</b> <?php _e('There is an additional cost for the number of subsites in Multisite Network.','miniorange-saml-20-single-sign-on');?></p>

            <li class="bottom-heading"> <?php _e('Multiple IdPs Supported','miniorange-saml-20-single-sign-on');?> -</li>
            <p style="padding-left:14px"><b>*</b> <?php _e('There is an additional cost for the IdPs if the number of IdP is more than 1.','miniorange-saml-20-single-sign-on');?></p>
            <br/>
            <p>
                <strong><?php _e('Note','miniorange-saml-20-single-sign-on');?> :</strong> <?php esc_html_e('miniOrange does not store or transfer any data which is coming from the Identity Provider to the WordPress. All the data remains within your premises / server. We do not provide the developer license for our paid plugins and the source code is protected. It is strictly prohibited to make any changes in the code without having written permission from miniOrange. There are hooks provided in the plugin which can be used by the developers to extend the plugin\'s functionality.','miniorange-saml-20-single-sign-on');?>
            </p>

            <h3><?php _e('10 Days Return Policy','miniorange-saml-20-single-sign-on');?> -</h3>
            <?php esc_html_e('At miniOrange, we want to ensure you are 100% happy with your purchase. If the premium plugin you purchased is
            not working as advertised and you\'ve attempted to resolve any issues with our support team, which couldn\'t get
            resolved. We will refund the whole amount within 10 days of the purchase.','miniorange-saml-20-single-sign-on');
            _e('Please email us at <b><a href="mailto:info@xecurify.com">info@xecurify.com</a></b>
            for any queries regarding the return policy.','miniorange-saml-20-single-sign-on');?>

        </div>
    </div>





    </div>

    <form style="display:none;" id="loginform"
                 action="<?php echo mo_saml_options_plugin_constants::HOSTNAME . '/moas/login'; ?>"
                 target="_blank" method="post">
        <input type="email" name="username" value="<?php echo get_option( 'mo_saml_admin_email' ); ?>"/>
        <input type="text" name="redirectUrl"
               value="<?php echo mo_saml_options_plugin_constants::HOSTNAME . '/moas/initializepayment'; ?>"/>
        <input type="text" name="requestOrigin" id="requestOrigin"/>
    </form>
    <a  id="mobacktoaccountsetup" style="display:none;" href="<?php echo mo_saml_add_query_arg( array( 'tab' => 'account-setup' ), htmlentities( $_SERVER['REQUEST_URI'] ) ); ?>"><?php _e('Back','miniorange-saml-20-single-sign-on');?></a>
    <style>

        .btn_blue{
            padding:5px !important;
            width:150px;
        }

        .table-onpremisetable{
            width: 30%;
            padding-top: 100px;
            margin: auto;
            width: 40%;
            padding: 10px;
        }


        .table-onpremisetable2{
            padding-top: 100px;
            margin: auto;
            width:	60%;
            padding: 10px;
            border: 2px solid #fff;
            table-layout:fixed;
            color: #173d50;

        }

        .table-onpremisetable2 th {
            background-color: #fcfdff;

            text-align: center;
            vertical-align:center;
        }

        .table-onpremisetable2 td {
            background-color: #fcfdff;

            text-align: center;
            vertical-align:center;
        }


        /* the third */
        .table-plugin-pricing{
            margin: auto;
            width: 70%;
            padding: 30px;
            background-color: transparent;
            border-collapse: collapse;
            border-spacing: 0;
        }

        /* .table-plugin-pricing td:nth-child(1) {
          width: 25%;
          height:auto;

          background-color: #fff !important;
          color: black;
          vertical-align: middle;


          } */

        /* the second */
        /* width: 20%;
        background-color: transparent;
        height:auto; */
        /* .table-plugin-pricing td:nth-child(2) {

              border: 1px solid #c4c4c4;
            min-width: 8%;
            padding: 10px 5px 10px 20px;
            word-break: normal;

        } */

        .give-some-space-dude{
            margin: 30px auto 45px;
        }


        .onpremise-container{
            color: black ;
            background-color: #fff !important;
        }

        .plugins-pricing{
            padding:50px;
            width:80%;
            margin: auto;
            background-color: inherit;
        }
        h1 {
            margin: .67em 0;
            font-size: 2em;
        }
        .tab-content-plugins-pricing div {
            background: #173d50;
        }

        /* .onpremise-container{
            background-color: #fff !important;
        } */
        .color-make-black{
            color:black;
        }
        .tip-icon {
            display: inline-block;
            width: 15px;
            height: 15px;
            background-image: url(https://cdn.auth0.com/website/assets/pages/pricing/img/tip-help-fc9f80876e.svg);
            background-size: 100%;
            background-repeat: no-repeat;
            background-position: 50%;
            vertical-align: middle;
            margin: 0 0 2px 5px;
            opacity: .3;
        }
    </style>
    <div class="support-icon">
        <div class="help-container" id="help-container">
      <span class="container-core">
        <div class="need">
          <span class="container-rel"></span>
            <div class="container-details">
              <div class="container-text">

                <span style="font-family:Trebuchet MS, Helvetica, sans-serif;color:#333333;"> Hello there! </span><br>
                <p class="helpline">Need Help? We are right here!</p>
              </div>
            </div>
        </div>

      </span>
        </div>

        <div class="service-btn" id="service-btn">
            <div class="service-icon">
                <img src="<?php echo plugin_dir_url(__FILE__).'images/mail.png';?>" class="service-img" alt="support">
            </div>
        </div>
    </div>

    <div class="support-form-container">
      <span class="container-rel"></span>
      <div class="widget-header" >
        <div class="widget-header-text"><h4>Contact miniOrange Support</h4></div>
        <div class="widget-header-close-icon">
            <button type="button" class="notice-dismiss" id="mo_saml_close_form">
                </button>
        </div>

      </div>

    <div class="loading-inner" style="overflow:hidden;">
      <div class="loading-icon">
        <div class="loading-icon-inner">
          <span class="icon-box">
            <img class="icon-image" src="<?php echo plugin_dir_url(__FILE__).'images/success.png';?>" alt="success">
          </span>
          <p class="loading-icon-text">
              <p>Thanks for your inquiry.<br><br>If you dont hear from us within 24 hours, please feel free to send a follow up email to <a href="mailto:<?php echo $supportmail;?>"><?php echo $supportmail;?></a></p>
          </p>
        </div>
      </div>
    </div>

    <div class="loading-inner-2" style="overflow:hidden;">
      <div class="loading-icon-2">
        <div class="loading-icon-inner-2">
          <br>
          <span class="icon-box-2">
            <img class="icon-image-2" src="<?php echo plugin_dir_url(__FILE__).'images/error.png';?>" alt="error">
          </span>
          <p class="loading-icon-text-2">
              <p>Unable to connect to Internet.<br>Please try again.</p>
          </p>
        </div>
      </div>
    </div>
    
    <div class="loading-inner-3" style="overflow:hidden;">
      <div class="loading-icon-3">
          <p class="loading-icon-text-3">
              <p style="font-size:18px;">Please Wait...</p>
          </p>
          <div class="loader"></div>
      </div>
    </div>
    

    <form role="form" action="" id="support-form" method="post" class="support-form top-label">
        <div class="field-group">
		  <label class="field-group-label" for="email">
			<span class="label-name">Your Contact E-mail</span>
		  </label>
		  <input type="email" class="field-label-text" style="background-color: #f1f1f1;" name="email" id="person_email" dir="auto"  required="true"  title="Enter a valid email address." placeholder="Enter valid email">
		</div>
		  <div class="field-group">
			  <label class="field-group-label">
				  <span class="label-name">What are you looking for</span>
			  </label >
			  <select class="what_you_looking_for" style="background-color: #f1f1f1; max-width:26.5rem;">
					<option class="Select-placeholder" value="" disabled>Select Category</option>
					<option value="Plugin Pricing">I want to discuss about Plugin Pricing</option>
					<option value="Schedule a Demo">I want to schedule a Demo</option>
					<option value="Custom Requirement">I have custom requirement</option>
					<option value="Others">My reason is not listed here </option>
			  </select>
		  </div>

		  <div class="field-group">
		  <label class="field-group-label" for="description">
			<span class="label-name">How can we help you?</span>
		  </label>
		  <textarea rows="5" id="person_query" name="description" dir="auto" required="true" class="field-label-textarea" placeholder="You will get reply via email"></textarea>
		</div>
		<div class="submit_button">
		  <button id="" type="submit" class="button1 button_new_color button__appearance-primary submit-button" value="Submit" aria-disabled="false">Submit</button>
		</div>
	  </form>
	</div>
    

    <script>
        jQuery("#mo_saml_close_form").click(function(){
            jQuery(".support-form-container").css('display','none');
        });

    </script>

    <script>
        jQuery(".help-container").click(function(){
            jQuery(".support-form-container").css('display','block');
            //jQuery(".help-container").css('display','none');
        });

        jQuery(".service-img").click(function(){
            jQuery('input[type="text"], textarea').val('');
            jQuery('select').val('');
            jQuery(".support-form-container").css('display','block');
            jQuery(".support-form").css('display','block');
            jQuery(".loading-inner").css('display','none');
            jQuery(".loading-inner-2").css('display','none');
            jQuery(".loading-inner-3").css('display','none');
            //jQuery(".help-container").css('display','none');
        });
    </script>

    <script>
    jQuery('.support-form').submit(function(e){
        e.preventDefault();
        
        var email = jQuery('#person_email').val();
        var query = jQuery('#person_query').val();
        var look= jQuery('.what_you_looking_for').val();
        var fname = "<?php echo $fname; ?>";
        var lname = "<?php echo $lname; ?>";

        if(look == '' || look == null){
            look = 'empty';
        }
       
        query1= '<b>['+look+']</b> <br><b>Plugin Licensing Question: </b>'+query+' <br> ';

        if(email == "" || query == "" || query1 == ""){

            jQuery('#login-error').show();
            jQuery('#errorAlert').show();

        }
        else{
            jQuery('input[type="text"], textarea').val('');
            jQuery('select').val('Select Category');
            jQuery(".support-form").css('display','none');
            jQuery(".loading-inner-3").css('display','block');
            var json = new Object();

            json = {
                "email" : email,
                "query" : query1,
                "ccEmail" : "samlsupport@xecurify.com",
                "company" : "<?= $_SERVER ['SERVER_NAME'] ?>",
                "firstName" : fname,
                "lastName" : lname,
            }
            
            var jsonString = JSON.stringify(json);
            jQuery.ajax({

                  url: "https://login.xecurify.com/moas/rest/customer/contact-us",
                  type : "POST",
                  data : jsonString,
                  crossDomain: true,
                  dataType : "text",
                  contentType : "application/json; charset=utf-8",
                  success: function (data, textStatus, xhr) { successFunction();},
                  error: function (jqXHR, textStatus, errorThrown) { errorFunction(); }

            });
           
        }
    });

    function successFunction(){
        
        jQuery(".loading-inner-3").css('display','none');
        jQuery(".loading-inner").css('display','block');
    }

    function errorFunction(){
        
        jQuery(".loading-inner-3").css('display','none');
        jQuery(".loading-inner-2").css('display','block');
    }
    </script>
    <script>

        function upgradeform(planType) {
            jQuery('#requestOrigin').val(planType);
            if(jQuery('#mo_customer_registered').val()==1)
                jQuery('#loginform').submit();
            else{
                location.href = jQuery('#mobacktoaccountsetup').attr('href');
            }

        }

        jQuery("input[name=sitetype]:radio").change(function() {

            if (this.value == 'multisite') {
                jQuery('.mosslp').removeClass('is-visible').addClass('is-hidden');
                jQuery('.momslp').addClass('is-visible').removeClass('is-hidden is-selected');
                document.getElementById("list-type").style.width = "133%";
            }
            else{
                document.getElementById("list-type").style.width = "100%";
            }
        });

        jQuery(document).ready(function($){



            //document.getElementById("multisite").checked = true;
            if(jQuery('#mo_license_plan_selected').val() == 'multisite'){
                document.getElementById("multisite").checked = true;
            }
            if(document.getElementById("multisite").checked == true){
                jQuery('.mosslp').removeClass('is-visible').addClass('is-hidden');
                jQuery('.momslp').addClass('is-visible').removeClass('is-hidden is-selected');
            }

            //hide the subtle gradient layer (.cd-pricing-list > li::after) when pricing table has been scrolled to the end (mobile version only)
            checkScrolling($('.cd-pricing-body'));
            $(window).on('resize', function(){
                window.requestAnimationFrame(function(){checkScrolling($('.cd-pricing-body'))});
            });
            $('.cd-pricing-body').on('scroll', function(){
                var selected = $(this);
                window.requestAnimationFrame(function(){checkScrolling(selected)});
            });

            function checkScrolling(tables){
                tables.each(function(){
                    var table= $(this),
                        totalTableWidth = parseInt(table.children('.cd-pricing-features').width()),
                        tableViewport = parseInt(table.width());
                    if( table.scrollLeft() >= totalTableWidth - tableViewport -1 ) {
                        table.parent('li').addClass('is-ended');
                    } else {
                        table.parent('li').removeClass('is-ended');
                    }
                });
            }

            //switch from monthly to annual pricing tables
            bouncy_filter($('.cd-pricing-container'));

            function bouncy_filter(container) {
                container.each(function(){
                    var pricing_table = $(this);
                    var filter_list_container = pricing_table.children('.cd-pricing-switcher'),
                        filter_radios = filter_list_container.find('input[type="radio"]'),
                        pricing_table_wrapper = pricing_table.find('.cd-pricing-wrapper');

                    //store pricing table items
                    var table_elements = {};
                    filter_radios.each(function(){
                        var filter_type = $(this).val();
                        table_elements[filter_type] = pricing_table_wrapper.find('li[data-type="'+filter_type+'"]');
                    });

                    //detect input change event
                    filter_radios.on('change', function(event){
                        event.preventDefault();
                        //detect which radio input item was checked
                        var selected_filter = $(event.target).val();

                        //give higher z-index to the pricing table items selected by the radio input
                        show_selected_items(table_elements[selected_filter]);

                        //rotate each cd-pricing-wrapper
                        //at the end of the animation hide the not-selected pricing tables and rotate back the .cd-pricing-wrapper

                        if( !Modernizr.cssanimations ) {
                            hide_not_selected_items(table_elements, selected_filter);
                            pricing_table_wrapper.removeClass('is-switched');
                        } else {
                            pricing_table_wrapper.addClass('is-switched').eq(0).one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function() {
                                hide_not_selected_items(table_elements, selected_filter);
                                pricing_table_wrapper.removeClass('is-switched');
                                //change rotation direction if .cd-pricing-list has the .cd-bounce-invert class
                                if(pricing_table.find('.cd-pricing-list').hasClass('cd-bounce-invert')) pricing_table_wrapper.toggleClass('reverse-animation');
                            });
                        }
                    });
                });
            }
            function show_selected_items(selected_elements) {
                selected_elements.addClass('is-selected');
            }

            function hide_not_selected_items(table_containers, filter) {
                $.each(table_containers, function(key, value){
                    if ( key != filter ) {
                        $(this).removeClass('is-visible is-selected').addClass('is-hidden');

                    } else {
                        $(this).addClass('is-visible').removeClass('is-hidden is-selected');
                    }
                });
            }
        });
    </script>
<?php
}