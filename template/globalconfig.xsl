<xsl:stylesheet
        version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

  <xsl:import href="base.xsl"/>

  <xsl:template match="/">
    <xsl:text disable-output-escaping='yes'>&lt;!DOCTYPE html&gt;</xsl:text>
    <html>
      <xsl:call-template name="head"/>
      <body>

        <div class="flex-grid">
          <div class="row">
            <xsl:call-template name="application-bar"/>
          </div>

          <div class="row">
            <div class="cell colspan12 padding20">
              <xsl:call-template name="content"/>
            </div>
          </div>
        </div>
        <xsl:call-template name="dialog"/>
        <script type="text/javascript">
          initGlobalconfig()
          initEdituser()
        </script>

      </body>
    </html>
  </xsl:template>

  <xsl:template name="content">

    <div class="tabcontrol2" data-role="tabcontrol">
      <ul class="tabs">
        <li>
          <a href="#profiles">Profiles</a>
        </li>
        <xsl:if test="/data/system/login/group = '1'">
          <li>
            <a href="#basic">Basis</a>
          </li>
          <li>
            <a href="#user">User</a>
          </li>
          <li>
            <a href="#saucelabs">Saucelabs</a>
          </li>
        </xsl:if>

      </ul>
      <div class="frames">
        <div class="frame" id="profiles">
          <xsl:call-template name="profile-settings"/>
          <xsl:call-template name="custom-browser-list"/>
        </div>

        <xsl:if test="/data/system/login/group = '1'">
          <div class="frame" id="user">
            <xsl:call-template name="user-settings"/>
          </div>

          <div class="frame" id="basic">
            <xsl:call-template name="base-settings"/>
          </div>
          <div class="frame" id="saucelabs">
            <xsl:call-template name="saucelabs-settings"/>
          </div>
        </xsl:if>
      </div>
    </div>
  </xsl:template>


  <xsl:template name="saucelabs-settings">
    <form action="" method="post">
      <div class="grid">
        <div class="row">

          <div class="input-control text">
            <label for="saucelabs_username">Username:</label>
            <input type="text"
                   class="form-control input-sm"
                   placeholder="Username"
                   value="{/data/system/globalconfig/saucelabs_username}"
                   name="saucelabs_username"
                   id="saucelabs_username"
            />

          </div>
        </div>
        <div class="row">
          <div class="input-control password" data-role="input">
            <label for="user_password">password:</label>
            <input type="password" name="password" id="saucelabs_password"/>
            <button class="button helper-button reveal">
              <span class="mif-looks"></span>
            </button>
          </div>
        </div>
        <div class="row">
          <div class="input-control password" data-role="input">
            <label for="access_key">access key:</label>
            <input type="access_key" name="access_key" value="{/data/system/globalconfig/access_key}" id="access_key"/>
            <button class="button helper-button reveal">
              <span class="mif-looks"></span>
            </button>
          </div>
        </div>

      </div>
      <input type="submit" name="save" value="Save" class="button primary"/>
      <input type="hidden" name="action" value="save_saucelabs"/>
    </form>
  </xsl:template>

  <xsl:template name="user-settings">

    <table class="table">
      <xsl:for-each select="/data/user/item">
        <xsl:variable name="cssClass">
          <xsl:choose>
            <xsl:when test="active='1'">
              success
            </xsl:when>
            <xsl:otherwise>
              error
            </xsl:otherwise>
          </xsl:choose>
        </xsl:variable>

        <tr class="{$cssClass}">
          <td>
            <xsl:value-of select="displayName"/>
          </td>
          <td>
            <xsl:value-of select="email"/>
          </td>
          <td>
            <a href="{/data/system/baseUrl}user/{id}/edit">
              <span class="mif-pencil"></span>
            </a>
            <xsl:text> </xsl:text>
            <xsl:choose>
              <xsl:when test="active='1'">
                <a href="{/data/system/baseUrl}user/{id}/inactivate" class="inactive">
                  <span class="mif-blocked"></span>
                </a>
                <xsl:text> </xsl:text>
              </xsl:when>
              <xsl:otherwise>
                <a href="{/data/system/baseUrl}user/{id}/activate" class="activate">
                  <span class="mif-checkmark"></span>
                </a>
                <xsl:text> </xsl:text>
              </xsl:otherwise>
            </xsl:choose>
            <a href="{/data/system/baseUrl}user/{id}/delete" class="delete">
              <span class="mif-cross"></span>
            </a>

          </td>
        </tr>

      </xsl:for-each>
    </table>

  </xsl:template>


  <xsl:template name="base-settings">
    <form action="" method="post">
      <xsl:call-template name="basic"/>
      <xsl:call-template name="ldap"/>
      <input type="submit" name="save" value="Save" class="button primary"/>
      <input type="hidden" name="action" value="savebase"/>
    </form>
  </xsl:template>

  <xsl:template name="profile-settings">
    <div id="addProfile-form">

      <form action="" method="post">
        <div class="grid">
          <div class="row cells4">
            <xsl:call-template name="profile-form"/>
          </div>

        </div>


        <input type="submit" name="save" value="Save" class="button primary"/>
        <input type="hidden" name="action" value="saveprofile"/>
      </form>
    </div>
  </xsl:template>

  <xsl:template name="profile-form">
    <div class="cell">
      <div class="input-control text">
        <input type="text"
               name="profileName"
               class="form-control"
               id="profileName"
               placeholder="profileName"
        />
      </div>
    </div>
    <div class="cell">
      <xsl:call-template name="browser"/>
    </div>
    <div class="cell" id="dimension" style="display:none;">
      <div class="group">
        <div class="input-control text">

          <input type="text"
                 class="form-control input-sm"
                 value=""
                 id="height"
                 name="height"
                 placeholder="height"
          />
        </div>
        <div class="input-control text">

          <input type="text"
                 class="form-control input-sm"
                 value=""
                 id="width"
                 name="width"
                 placeholder="width"
          />
        </div>
      </div>
    </div>
    <div class="cell" style="display: none;" id="devices">
      <xsl:call-template name="devices"/>
    </div>

  </xsl:template>


  <xsl:template name="devices">
    <div class="input-control select">
      <select name="device">
        <option value="">Device</option>
        <xsl:for-each select="/data/devices/item">
          <option value="{value}_portrait">
            <xsl:value-of select="title"/> Portrait
          </option>
          <option value="{value}_landscape">
            <xsl:value-of select="title"/> Landscape
          </option>
        </xsl:for-each>
      </select>
    </div>
  </xsl:template>

  <xsl:template name="browser">
    <div class="cell">

      <div class="input-control select">
        <select name="browser" id="browsers">
          <option value="">basis browser</option>
          <xsl:for-each select="/data/profiles/grid/item">
            <xsl:choose>
              <xsl:when test="version = ''">
                <option value="{browserName}_default_{platform}">
                  <xsl:value-of select="browserName"/><xsl:text> </xsl:text>
                  <xsl:value-of select="default"/><xsl:text> </xsl:text>
                  <xsl:value-of select="platform"/>
                </option>
              </xsl:when>
              <xsl:otherwise>
                <option value="{browserName}_{version}_{platform}">
                  <xsl:value-of select="browserName"/><xsl:text> </xsl:text>
                  <xsl:value-of select="version"/><xsl:text> </xsl:text>
                  <xsl:value-of select="platform"/>
                </option>
              </xsl:otherwise>
            </xsl:choose>

          </xsl:for-each>
        </select>
      </div>
    </div>

  </xsl:template>


  <xsl:template name="ldap">
    <div class="grid">
      <div class="row">
        <div class="input-control text">
          <label for="hostname">Ldap Hostname:</label>
          <input type="text"
                 class="form-control input-sm"
                 placeholder="Ldap Hostname"
                 aria-describedby="basic-addon2"
                 value="{/data/system/globalconfig/ldapHostname}"
                 name="ldapHostname"
                 id="hostname"
          />
        </div>
      </div>
      <div class="row">
        <div class="input-control text">
          <label for="ldapBaseDn">base Dn:</label>
          <input type="text"
                 class="form-control input-sm"
                 placeholder="base Dn"
                 aria-describedby="basic-addon2"
                 value="{/data/system/globalconfig/ldapBaseDn}"
                 name="ldapBaseDn"
                 id="baseDn"
          />
        </div>
      </div>
      <div class="row">

        <div class="input-control text">
          <label for="ldapPassword">LDAP CN:</label>
          <input type="text"
                 class="form-control input-sm"
                 placeholder="ldap cn"
                 aria-describedby="basic-addon2"
                 value="{/data/system/globalconfig/ldapCn}"
                 name="ldapCn"
                 id="ldapCn"
          />
        </div>
      </div>
      <div class="row">

        <div class="input-control text">
          <label for="ldapPassword">LDAP password:</label>
          <div class="input-control password full-size" data-role="input">
            <input type="password" name="ldapPassword" id="ldapPassword"/>
            <button class="button helper-button reveal">
              <span class="mif-looks"></span>
            </button>
          </div>
        </div>
      </div>
      <div class="row">

        <label class="input-control checkbox">
          <label for="useLdap">Use LDAP:</label>
          <input type="checkbox"
                 value="true"
                 name="useLdap"
                 id="useLdap"
          >
            <xsl:if test="/data/system/globalconfig/useLdap = '1'">
              <xsl:attribute name="checked">checked</xsl:attribute>
            </xsl:if>
          </input>
          <span class="check"></span>
        </label>
      </div>
    </div>
  </xsl:template>


  <xsl:template name="basic">

    <div class="grid">
      <div class="row">
        <div class="input-control text">
          <label for="appPath">App Path:</label>
          <input type="text"
                 class="form-control input-sm"
                 placeholder="App Path"
                 aria-describedby="basic-addon2"
                 value="{/data/system/globalconfig/appPath}"
                 name="appPath"
                 id="appPath"
          />

        </div>
      </div>
      <div class="row">
        <div class="input-control text">
          <label for="selAddr">Selenium Hub:</label>
          <input type="text"
                 class="form-control input-sm"
                 placeholder="Selenium Hub: http://xxx.xxx.xxx.xxx:4444"
                 value="{/data/system/globalconfig/seleniumAddress}"
                 name="seleniumAddress"
                 id="selAddr"
          />
        </div>
      </div>
    </div>
  </xsl:template>

  <xsl:template name="custom-browser-list">

    <xsl:for-each select="/data/profiles/custom/item">
      <div class="panel collapsed" style="margin:0.75rem 0;" data-role="panel">
        <div class="heading">
          <xsl:call-template name="browser-icon">
            <xsl:with-param name="browser" select="browser"/>
          </xsl:call-template>
          <span class="title">
            <xsl:value-of select="name"/>
            <a href="" data-profilename="{name}" data-action="deleteProfile"
               style="float:right; color:#fff; margin-right:2.50rem;">
              <span class="mif-cross"></span>
            </a>
            <a href="" data-profilename="{name}" data-action="editProfile"
               style="float:right; color:#fff; margin-right:0.75rem;">
              <span class="mif-pencil"></span>
            </a>
          </span>
        </div>
        <div class="content padding10">
          <xsl:if test="driverOptions/dimensions">
            <h5>Window settings:</h5>
            Width=
            <xsl:value-of select="driverOptions/dimensions/width"/> px
            <br/>
            Height=
            <xsl:value-of select="driverOptions/dimensions/height"/> px
            <br/>
            <hr/>
          </xsl:if>

          <xsl:if test="arguments/*">
            <h5>Browser Arguments:</h5>
            <ul class="simple-list">
              <xsl:for-each select="arguments/*">
                <li>
                  <xsl:value-of select="name(.)"/>
                </li>
              </xsl:for-each>
            </ul>
            <hr/>
          </xsl:if>

          <xsl:if test="capabilities/*">
            <h5>Capabilities:</h5>
            Arguments:
            <br/>
            <ul class="simple-list">
              <xsl:for-each select="capabilities/arguments/item">
                <li>
                  <xsl:value-of select="."/>
                </li>
              </xsl:for-each>
            </ul>
            <hr/>
            <xsl:if test="capabilities/experimental/mobileEmulation/deviceName">
              <h5>Device:</h5>
              <xsl:value-of select="capabilities/experimental/mobileEmulation/deviceName"/>
            </xsl:if>


          </xsl:if>

        </div>
      </div>
    </xsl:for-each>
  </xsl:template>


  <xsl:template name="browser-icon">
    <xsl:param name="browser"/>
    <xsl:variable name="browserBase" select="substring-before($browser,'_')"/>

    <xsl:variable name="imageUrl">
      <xsl:choose>
        <xsl:when test="$browserBase = 'internetExplorer'"><xsl:value-of select="/data/system/baseUrl"/>css/images/ie.png
        </xsl:when>
        <xsl:when test="$browserBase = 'edge'"><xsl:value-of select="/data/system/baseUrl"/>css/images/edge.png
        </xsl:when>
        <xsl:when test="$browserBase = 'chrome'"><xsl:value-of select="/data/system/baseUrl"/>css/images/chrome.png
        </xsl:when>
        <xsl:when test="$browserBase = 'firefox'"><xsl:value-of select="/data/system/baseUrl"/>css/images/firefox.png
        </xsl:when>
        <xsl:otherwise><xsl:value-of select="/data/system/baseUrl"/>css/images/browser.png
        </xsl:otherwise>
      </xsl:choose>
    </xsl:variable>
    <img class="icon" src="{$imageUrl}"/>
  </xsl:template>

  <xsl:template name="dialog">
    <div data-role="dialog" id="dialog" class="padding20">
      <h4 class="dialogTitle"></h4>
      <p class="dialogContent"></p>
      <button class="button primary" id="dialogButton">Ok</button>
      <xsl:text> </xsl:text>
      <a href="" class="button" id="dialogButtonClose">Cancel</a>
    </div>
  </xsl:template>
</xsl:stylesheet>