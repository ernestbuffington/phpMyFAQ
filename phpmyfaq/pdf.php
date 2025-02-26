<?php

/**
 * PDF export.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   phpMyFAQ
 * @author    Thorsten Rinne <thorsten@phpmyfaq.de>
 * @author    Peter Beauvain <pbeauvain@web.de>
 * @author    Olivier Plathey <olivier@fpdf.org>
 * @author    Krzysztof Kruszynski <thywolf@wolf.homelinux.net>
 * @author    Matteo Scaramuccia <matteo@phpmyfaq.de>
 * @copyright 2003-2023 phpMyFAQ Team
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      https://www.phpmyfaq.de
 * @since     2003-02-12
 */

use phpMyFAQ\Category;
use phpMyFAQ\Export\Pdf;
use phpMyFAQ\Faq;
use phpMyFAQ\Filter;
use phpMyFAQ\Helper\HttpHelper;
use phpMyFAQ\Language;
use phpMyFAQ\Language\Plurals;
use phpMyFAQ\Strings;
use phpMyFAQ\Tags;
use phpMyFAQ\Translation;
use phpMyFAQ\User\CurrentUser;

const IS_VALID_PHPMYFAQ = null;

//
// Bootstrapping
//
require 'src/Bootstrap.php';

// get language (default: english)
$Language = new Language($faqConfig);
$faqLangCode = $Language->setLanguage($faqConfig->get('main.languageDetection'), $faqConfig->get('main.language'));
$faqConfig->setLanguage($Language);

// Found an article language?
$lang = Filter::filterInput(INPUT_POST, 'artlang', FILTER_UNSAFE_RAW);
if (is_null($lang) && !Language::isASupportedLanguage($lang)) {
    $lang = Filter::filterInput(INPUT_GET, 'artlang', FILTER_UNSAFE_RAW);
    if (is_null($lang) && !Language::isASupportedLanguage($lang)) {
        $lang = $faqLangCode;
    }
}

if (isset($lang) && Language::isASupportedLanguage($lang)) {
    require_once 'lang/language_' . $lang . '.php';
} else {
    $lang = 'en';
    require_once 'lang/language_en.php';
}

//
// Set translation class
//
try {
    Translation::create()
        ->setLanguagesDir(PMF_LANGUAGE_DIR)
        ->setDefaultLanguage('en')
        ->setCurrentLanguage($faqLangCode);
} catch (Exception $e) {
    echo '<strong>Error:</strong> ' . $e->getMessage();
}

//
// Load plurals support for selected language
//
$plr = new Plurals();

//
// Initializing static string wrapper
//
Strings::init($faqLangCode);

// authenticate with session information
[ $user, $auth ] = CurrentUser::getCurrentUser($faqConfig);

// Get current user and group id - default: -1
[ $currentUser, $currentGroups ] = CurrentUser::getCurrentUserGroupId($user);

$currentCategory = Filter::filterInput(INPUT_GET, 'cat', FILTER_VALIDATE_INT);
$id = Filter::filterInput(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$getAll = Filter::filterInput(INPUT_GET, 'getAll', FILTER_VALIDATE_BOOLEAN, false);

$faq = new Faq($faqConfig);
$faq->setUser($currentUser);
$faq->setGroups($currentGroups);

$category = new Category($faqConfig, $currentGroups, true);
$category->setUser($currentUser);

try {
    $pdf = new Pdf($faq, $category, $faqConfig);
} catch (Exception) {
    // handle exception
}
$http = new HttpHelper();

if (true === $getAll) {
    $category->buildCategoryTree();
}
$tags = new Tags($faqConfig);

$headers = [
    'Pragma: no-cache',
    'Expires: 0',
    'Cache-Control: no-store',
];

if (true === $getAll && $user->perm->hasPermission($user->getUserId(), 'export')) {
    $filename = 'FAQs.pdf';
    $pdfFile = $pdf->generate(0, true, $lang);
} else {
    if (is_null($currentCategory) || is_null($id)) {
        $http->redirect($faqConfig->getDefaultUrl());
        exit();
    }

    $faq->getRecord($id);
    $faq->faqRecord['category_id'] = $currentCategory;

    $filename = 'FAQ-' . $id . '-' . $lang . '.pdf';
    $pdfFile = $pdf->generateFile($faq->faqRecord, $filename);
}

if (preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT'])) {
    $headers[] = 'Content-type: application/pdf';
    $headers[] = 'Content-Transfer-Encoding: binary';
    $headers[] = 'Content-Disposition: attachment; filename=' . $filename;
} else {
    $headers[] = 'Content-Type: application/pdf';
}

$http->sendWithHeaders($pdfFile, $headers);
