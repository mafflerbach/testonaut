<?php
/**
 * Created by PhpStorm.
 * User: maren
 * Date: 08.03.2015
 * Time: 21:57
 */

namespace testonaut;


class Capabilities extends \DesiredCapabilities {

  /**
   * @return DesiredCapabilities
   */
  public static function iexplore() {
    return new \DesiredCapabilities(array(
      \WebDriverCapabilityType::BROWSER_NAME => \WebDriverBrowserType::IEXPLORE,
      \WebDriverCapabilityType::PLATFORM => \WebDriverPlatform::WINDOWS,
    ));
  }

} 