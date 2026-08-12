<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Kanakku provides clean Admin Templates for managing Sales, Payment, Invoice, Accounts and Expenses in HTML, Bootstrap 5, ReactJs, Angular, VueJs and Laravel.">
    <meta name="keywords"
        content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
    <meta name="author" content="Dreamguys - Bootstrap Admin Template">
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@dreamstechnologies">
    <meta name="twitter:title" content="FInoodexInventory">
    <meta name="twitter:description"
        content="Kanakku is a Sales, Invoices & Accounts Admin template for Accountant or Companies/Offices with various features for all your needs. Try Demo and Buy Now.">
    <meta name="twitter:image" content="../../assets/img/kanakku.html">
    <meta name="twitter:image:alt" content="Kanakku">

    <!-- Facebook -->
    <meta property="og:url" content="https://kanakku.dreamstechnologies.com/">
    <meta property="og:title" content="FInoodexInventory">
    <meta property="og:description"
        content="Kanakku is a Sales, Invoices & Accounts Admin template for Accountant or Companies/Offices with various features for all your needs. Try Demo and Buy Now.">
    <meta property="og:image" content="../../assets/img/kanakku.html">
    <meta property="og:image:secure_url" content="../../assets/img/kanakku.html">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="600">
    <title>Inoodex Inventory</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets') }}/img/logo.jpg">

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

    <!-- Layout JS -->
    <script src="{{asset('assets')}}/js/layout.js"></script>

</head>
