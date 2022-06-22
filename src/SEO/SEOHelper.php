<?php
/**
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Notamedia\ConsoleJedi\SEO;

class SEOHelper
{
    function seoSitemapGetFilesData($PID, $arSitemap, $arCurrentDir, $sitemapFile)
    {
        global $NS;

        $arDirList = array();

        if ($arCurrentDir['ACTIVE'] == SitemapRuntimeTable::ACTIVE) {
            $list = \CSeoUtils::getDirStructure(
                $arSitemap['SETTINGS']['logical'] == 'Y',
                $arSitemap['SITE_ID'],
                $arCurrentDir['ITEM_PATH']
            );

            foreach ($list as $dir) {
                $dirKey = "/" . ltrim($dir['DATA']['ABS_PATH'], "/");

                if ($dir['TYPE'] == 'F') {
                    if (!isset($arSitemap['SETTINGS']['FILE'][$dirKey])
                        || $arSitemap['SETTINGS']['FILE'][$dirKey] == 'Y') {
                        if (preg_match($arSitemap['SETTINGS']['FILE_MASK_REGEXP'], $dir['FILE'])) {
                            $f = new IO\File($dir['DATA']['PATH'], $arSitemap['SITE_ID']);
                            $sitemapFile->addFileEntry($f);
                            $NS['files_count']++;
                        }
                    }
                } else {
                    if (!isset($arSitemap['SETTINGS']['DIR'][$dirKey])
                        || $arSitemap['SETTINGS']['DIR'][$dirKey] == 'Y') {
                        $arDirList[] = $dirKey;
                    }
                }
            }
        } else {
            $len = mb_strlen($arCurrentDir['ITEM_PATH']);
            if (!empty($arSitemap['SETTINGS']['DIR'])) {
                foreach ($arSitemap['SETTINGS']['DIR'] as $dirKey => $checked) {
                    if ($checked == 'Y') {
                        if (strncmp($arCurrentDir['ITEM_PATH'], $dirKey, $len) === 0) {
                            $arDirList[] = $dirKey;
                        }
                    }
                }
            }

            if (!empty($arSitemap['SETTINGS']['FILE'])) {
                foreach ($arSitemap['SETTINGS']['FILE'] as $dirKey => $checked) {
                    if ($checked == 'Y') {
                        if (strncmp($arCurrentDir['ITEM_PATH'], $dirKey, $len) === 0) {
                            $fileName = IO\Path::combine(
                                SiteTable::getDocumentRoot($arSitemap['SITE_ID']),
                                $dirKey
                            );

                            if (!is_dir($fileName)) {
                                $f = new IO\File($fileName, $arSitemap['SITE_ID']);
                                if ($f->isExists()
                                    && !$f->isSystem()
                                    && preg_match($arSitemap['SETTINGS']['FILE_MASK_REGEXP'], $f->getName())
                                ) {
                                    $sitemapFile->addFileEntry($f);
                                    $NS['files_count']++;
                                }
                            }
                        }
                    }
                }
            }
        }

        if (count($arDirList) > 0) {
            foreach ($arDirList as $dirKey) {
                $arRuntimeData = array(
                    'PID' => $PID,
                    'ITEM_PATH' => $dirKey,
                    'PROCESSED' => SitemapRuntimeTable::UNPROCESSED,
                    'ACTIVE' => SitemapRuntimeTable::ACTIVE,
                    'ITEM_TYPE' => SitemapRuntimeTable::ITEM_TYPE_DIR,
                );
                SitemapRuntimeTable::add($arRuntimeData);
            }
        }

        SitemapRuntimeTable::update($arCurrentDir['ID'], array(
            'PROCESSED' => SitemapRuntimeTable::PROCESSED
        ));
    }
}
