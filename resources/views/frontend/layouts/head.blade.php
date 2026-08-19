<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Itec Inventory</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets') }}/img/logo.jpg">

    <!-- Theme Switcher (Dark/Light) -->
    <script>
        (function () {
            var key = 'dp-theme-mode', saved = null;
            try { saved = localStorage.getItem(key); } catch (e) {}
            var mode = (saved === 'dark' || saved === 'light') ? saved : null;
            if (mode) {
                document.documentElement.setAttribute('data-layout-mode', mode);
                try { sessionStorage.setItem('data-layout-mode', mode); } catch (e) {}
            }
            window.dpThemeToggle = function () {
                var cur = document.documentElement.getAttribute('data-layout-mode');
                var next = cur === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-layout-mode', next);
                try {
                    localStorage.setItem(key, next);
                    sessionStorage.setItem('data-layout-mode', next);
                } catch (e) {}
                var i = document.getElementById('theme-toggle-icon');
                if (i) {
                    i.className = 'fe ' + (next === 'dark' ? 'fe-sun' : 'fe-moon');
                    i.classList.remove('theme-spin');
                    void i.offsetWidth;
                    i.classList.add('theme-spin');
                }
            };
            document.addEventListener('DOMContentLoaded', function () {
                var cur = document.documentElement.getAttribute('data-layout-mode');
                var i = document.getElementById('theme-toggle-icon');
                if (i) i.className = 'fe ' + (cur === 'dark' ? 'fe-sun' : 'fe-moon');
            });
        })();
    </script>

    <!-- Theme Switcher Styling -->
    <style>
        .theme-switch-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #3F4254;
            font-size: 28px;
            text-decoration: none !important;
            padding: 4px;
            transition: color 0.25s ease, opacity 0.25s ease;
        }
        .theme-switch-btn:hover {
            color: #f97316;
            opacity: 0.8;
        }
        .theme-switch-btn i {
            display: inline-block;
            font-size: 28px !important;
            line-height: 1;
        }
        .theme-switch-btn i.theme-spin {
            animation: themeIconSpin 0.4s ease;
        }
        @keyframes themeIconSpin {
            0% { transform: rotate(0deg) scale(0.5); opacity: 0; }
            100% { transform: rotate(360deg) scale(1); opacity: 1; }
        }
        html[data-layout-mode="dark"] .theme-switch-btn {
            color: #AAAAAA;
        }
        html[data-layout-mode="dark"] .theme-switch-btn:hover {
            color: #38bdf8;
        }
    </style>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap.min.css">

    <!-- Font family -->
    <style type="text/css">
        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 100;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/cyrillic/wght/normal.woff2);
            unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 100;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/vietnamese/wght/normal.woff2);
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 100;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/greek/wght/normal.woff2);
            unicode-range: U+0370-03FF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 100;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/greek-ext/wght/normal.woff2);
            unicode-range: U+1F00-1FFF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 100;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/cyrillic-ext/wght/normal.woff2);
            unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 100;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/latin/wght/normal.woff2);
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 100;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/latin-ext/wght/normal.woff2);
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 200;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/latin/wght/normal.woff2);
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 200;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/cyrillic/wght/normal.woff2);
            unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 200;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/greek/wght/normal.woff2);
            unicode-range: U+0370-03FF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 200;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/vietnamese/wght/normal.woff2);
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 200;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/cyrillic-ext/wght/normal.woff2);
            unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 200;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/greek-ext/wght/normal.woff2);
            unicode-range: U+1F00-1FFF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 200;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/latin-ext/wght/normal.woff2);
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 300;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/cyrillic-ext/wght/normal.woff2);
            unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 300;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/greek-ext/wght/normal.woff2);
            unicode-range: U+1F00-1FFF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 300;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/greek/wght/normal.woff2);
            unicode-range: U+0370-03FF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 300;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/cyrillic/wght/normal.woff2);
            unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 300;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/latin-ext/wght/normal.woff2);
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 300;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/vietnamese/wght/normal.woff2);
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 300;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/latin/wght/normal.woff2);
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 400;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/cyrillic-ext/wght/normal.woff2);
            unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 400;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/latin-ext/wght/normal.woff2);
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 400;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/cyrillic/wght/normal.woff2);
            unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 400;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/vietnamese/wght/normal.woff2);
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 400;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/greek-ext/wght/normal.woff2);
            unicode-range: U+1F00-1FFF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 400;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/greek/wght/normal.woff2);
            unicode-range: U+0370-03FF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 400;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/latin/wght/normal.woff2);
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 500;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/greek/wght/normal.woff2);
            unicode-range: U+0370-03FF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 500;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/vietnamese/wght/normal.woff2);
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 500;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/latin-ext/wght/normal.woff2);
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 500;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/cyrillic/wght/normal.woff2);
            unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 500;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/cyrillic-ext/wght/normal.woff2);
            unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 500;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/latin/wght/normal.woff2);
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 500;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/greek-ext/wght/normal.woff2);
            unicode-range: U+1F00-1FFF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 600;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/latin/wght/normal.woff2);
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 600;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/latin-ext/wght/normal.woff2);
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 600;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/cyrillic-ext/wght/normal.woff2);
            unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 600;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/greek-ext/wght/normal.woff2);
            unicode-range: U+1F00-1FFF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 600;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/cyrillic/wght/normal.woff2);
            unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 600;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/greek/wght/normal.woff2);
            unicode-range: U+0370-03FF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 600;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/vietnamese/wght/normal.woff2);
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 700;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/latin-ext/wght/normal.woff2);
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 700;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/vietnamese/wght/normal.woff2);
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 700;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/greek-ext/wght/normal.woff2);
            unicode-range: U+1F00-1FFF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 700;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/latin/wght/normal.woff2);
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 700;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/cyrillic/wght/normal.woff2);
            unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 700;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/cyrillic-ext/wght/normal.woff2);
            unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 700;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/greek/wght/normal.woff2);
            unicode-range: U+0370-03FF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 800;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/latin-ext/wght/normal.woff2);
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 800;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/vietnamese/wght/normal.woff2);
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 800;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/greek/wght/normal.woff2);
            unicode-range: U+0370-03FF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 800;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/latin/wght/normal.woff2);
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 800;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/cyrillic-ext/wght/normal.woff2);
            unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 800;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/cyrillic/wght/normal.woff2);
            unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 800;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/greek-ext/wght/normal.woff2);
            unicode-range: U+1F00-1FFF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 900;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/latin/wght/normal.woff2);
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 900;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/vietnamese/wght/normal.woff2);
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 900;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/cyrillic/wght/normal.woff2);
            unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 900;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/greek-ext/wght/normal.woff2);
            unicode-range: U+1F00-1FFF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 900;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/cyrillic-ext/wght/normal.woff2);
            unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 900;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/greek/wght/normal.woff2);
            unicode-range: U+0370-03FF;
            font-display: swap;
        }

        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 900;
            src: url(https://kanakku.dreamstechnologies.com/cf-fonts/v/inter/5.0.16/latin-ext/wght/normal.woff2);
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            font-display: swap;
        }
    </style>

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/fontawesome/css/all.min.css">

    <!-- Feather CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/feather/feather.css">

    <!-- Datepicker CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap-datetimepicker.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">

    <!-- jQuery -->
    <script src="{{ asset('assets') }}/js/jquery-3.7.1.min.js"></script>

    <!-- Select2 CSS & JS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/select2/css/select2.min.css">
    <script src="{{ asset('assets') }}/plugins/select2/js/select2.min.js"></script>
    <!-- Select2 Global Styling & Arrow Fixes -->
    <style>
        select.select2, select.form-select.select2 {
            background-image: none !important;
        }
        .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1px solid #dbe2ea !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
            background-color: #ffffff !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            color: #2c3038 !important;
            padding-left: 12px !important;
            padding-right: 28px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            position: absolute !important;
            top: 1px !important;
            right: 8px !important;
            width: 20px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #6c757d transparent transparent transparent !important;
            border-style: solid !important;
            border-width: 5px 4px 0 4px !important;
            height: 0 !important;
            left: 50% !important;
            margin-left: -4px !important;
            margin-top: -2px !important;
            position: absolute !important;
            top: 50% !important;
            width: 0 !important;
        }
        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #6c757d transparent !important;
            border-width: 0 4px 5px 4px !important;
        }
    </style>

    <!-- Readonly/Disabled Fields: Consistent Look in Both Modes -->
    <style>
        .form-control[readonly],
        .form-control:read-only,
        .form-control:disabled,
        .form-select[readonly],
        .form-select:disabled,
        input[readonly],
        textarea[readonly] {
            background-color: #eef2f7 !important;
            color: #475569 !important;
        }
        html[data-layout-mode="dark"] .form-control[readonly],
        html[data-layout-mode="dark"] .form-control:read-only,
        html[data-layout-mode="dark"] .form-control:disabled,
        html[data-layout-mode="dark"] .form-select[readonly],
        html[data-layout-mode="dark"] .form-select:disabled,
        html[data-layout-mode="dark"] input[readonly],
        html[data-layout-mode="dark"] textarea[readonly] {
            background-color: #22262d !important;
            color: #aeb8c4 !important;
        }
    </style>

    <!-- Dark Mode: Form Controls Fix -->
    <style>
        html[data-layout-mode="dark"] .form-control,
        html[data-layout-mode="dark"] .form-select,
        html[data-layout-mode="dark"] .form-control.bg-white,
        html[data-layout-mode="dark"] .form-select.bg-white,
        html[data-layout-mode="dark"] .form-control.bg-light,
        html[data-layout-mode="dark"] .form-select.bg-light {
            background-color: #1b1e23 !important;
            border-color: #2e3840 !important;
            color: #e6e9ef !important;
        }
        html[data-layout-mode="dark"] .form-control:focus,
        html[data-layout-mode="dark"] .form-select:focus {
            background-color: #1b1e23 !important;
            border-color: #7638ff !important;
            color: #e6e9ef !important;
            box-shadow: 0 0 0 0.2rem rgba(118, 56, 255, 0.15) !important;
        }
        html[data-layout-mode="dark"] .form-control::placeholder,
        html[data-layout-mode="dark"] .form-select::placeholder {
            color: #7b8794 !important;
        }
        html[data-layout-mode="dark"] .form-control[readonly],
        html[data-layout-mode="dark"] .form-control:read-only {
            background-color: #22262d !important;
            color: #aeb8c4 !important;
        }
        html[data-layout-mode="dark"] .form-control[style],
        html[data-layout-mode="dark"] .form-select[style] {
            background-color: #22262d !important;
            color: #aeb8c4 !important;
        }
        html[data-layout-mode="dark"] .form-check-input {
            background-color: #1b1e23;
            border-color: #3a4550;
        }
        html[data-layout-mode="dark"] .form-check-input:checked {
            background-color: #7638ff;
            border-color: #7638ff;
        }
        html[data-layout-mode="dark"] .table thead.bg-light,
        html[data-layout-mode="dark"] .table thead.bg-white {
            background-color: #20242b !important;
        }
        html[data-layout-mode="dark"] .select2-container--default .select2-selection--single {
            background-color: #1b1e23 !important;
            border-color: #2e3840 !important;
        }
        html[data-layout-mode="dark"] .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #e6e9ef !important;
        }
        html[data-layout-mode="dark"] .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #8a94a0 transparent transparent transparent !important;
        }
        html[data-layout-mode="dark"] .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #8a94a0 transparent !important;
        }
        html[data-layout-mode="dark"] .select2-dropdown,
        html[data-layout-mode="dark"] .select2-container--default .select2-results__option {
            background-color: #1b1e23 !important;
            color: #e6e9ef !important;
        }
        html[data-layout-mode="dark"] .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #7638ff !important;
            color: #ffffff !important;
        }
        html[data-layout-mode="dark"] .select2-container--default .select2-search--dropdown .select2-search__field {
            background-color: #14161a !important;
            color: #e6e9ef !important;
            border-color: #2e3840 !important;
        }
    </style>

    <!-- Table Responsive & Dropdown Safeguards -->
    <style>
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table-responsive .dropdown-menu {
            z-index: 1080 !important;
        }
    </style>

    <!-- Page Headings Raised Up -->
    <style>
        .page-wrapper .content {
            padding-top: 12px !important;
        }
        .page-header {
            margin-top: -10px !important;
        }
        @media (max-width: 991.98px) {
            .page-wrapper .content {
                padding-top: 8px !important;
            }
            .page-header {
                margin-top: -6px !important;
            }
        }
    </style>

    @stack('styles')

    <!-- No Whole-Row Hover on Custom Tables (all pages) -->
    <style>
        .table-custom > tbody > tr:hover {
            background-color: transparent !important;
        }
        .table-custom > tbody > tr:hover > * {
            --bs-table-accent-bg: transparent !important;
            --bs-table-bg-state: transparent !important;
            --bs-table-hover-bg: transparent !important;
        }
        .table-custom > tbody > tr > td {
            transition: background 0.25s ease;
        }
        .table-custom > tbody > tr > td:hover {
            background: rgba(25, 25, 112, 0.05);
            box-shadow: none;
        }
        html[data-layout-mode="dark"] .table-custom > tbody > tr > td:hover {
            background: rgba(99, 102, 241, 0.10);
        }
    </style>

    <!-- Dark Mode: Same Background as Dashboard (all pages) -->
    <style>
        html[data-layout-mode="dark"] body {
            background-color: #0f172a !important;
        }
        html[data-layout-mode="dark"] .page-wrapper {
            background-color: #0f172a !important;
        }
        html[data-layout-mode="dark"] .page-wrapper .content {
            background-color: #0f172a !important;
        }
        html[data-layout-mode="dark"] .sidebar,
        html[data-layout-mode="dark"] body .sidebar {
            background-color: #0f172a !important;
        }
        html[data-layout-mode="dark"] .table thead,
        html[data-layout-mode="dark"] .table tfoot {
            background-color: #1b1e23 !important;
            color: #e6e9ef !important;
        }
        html[data-layout-mode="dark"] .table tbody tr[style] {
            background-color: #1b1e23 !important;
        }
        html[data-layout-mode="dark"] .bg-light {
            background-color: #262b33 !important;
        }
        html[data-layout-mode="dark"] .bg-white {
            background-color: #1b1e23 !important;
        }
        html[data-layout-mode="dark"] .alert-success {
            background-color: #14261c !important;
            border-color: #1f3a2a !important;
            color: #9fe8bf !important;
        }
        html[data-layout-mode="dark"] .alert-success .alert-heading {
            color: #bdf2d3 !important;
        }
        html[data-layout-mode="dark"] .alert-danger {
            background-color: #2b1c1c !important;
            border-color: #4a2c2c !important;
            color: #f3a6a6 !important;
        }
        html[data-layout-mode="dark"] .alert-danger .alert-heading {
            color: #f7c4c4 !important;
        }
        html[data-layout-mode="dark"] .alert-warning {
            background-color: #2b2416 !important;
            border-color: #4a3d22 !important;
            color: #f0d48a !important;
        }
        html[data-layout-mode="dark"] .alert-warning .alert-heading {
            color: #f7e3a8 !important;
        }
        html[data-layout-mode="dark"] .alert-info {
            background-color: #142430 !important;
            border-color: #1f3a4d !important;
            color: #9fd8ef !important;
        }
        html[data-layout-mode="dark"] .alert-info .alert-heading {
            color: #c2eafb !important;
        }
        html[data-layout-mode="dark"] .alert-primary {
            background-color: #1d1b2e !important;
            border-color: #332f4d !important;
            color: #c4bdf2 !important;
        }
        html[data-layout-mode="dark"] .alert-primary .alert-heading {
            color: #d8d3f8 !important;
        }
    </style>

    <!-- Logo Design -->
    <style>
        .main-logo a,
        .device-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            transition: transform 0.25s ease;
        }
        .main-logo a:hover,
        .device-logo:hover {
            transform: translateY(-1px);
        }
        .main-logo img,
        .device-logo img {
            width: auto;
            height: 38px;
            object-fit: contain;
        }
    </style>

    <!-- Layout JS -->
    <script src="{{asset('assets')}}/js/layout.js"></script>

</head>
