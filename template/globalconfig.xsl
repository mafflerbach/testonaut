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
        </xsl:if>
      </div>
    </div>
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
        <xsl:for-each select="/data/devices/*">
          <option value="{name(.)}">
            <xsl:value-of select="."/>
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

          <input type="text"
                 class="form-control input-sm"
                 placeholder="Ldap Password"
                 aria-describedby="basic-addon2"
                 value="{/data/system/globalconfig/ldapPassword}"
                 name="ldapPassword"
                 id="ldapPassword"
          />
        </div>
      </div>
      <div class="row">

        <label class="input-control checkbox">

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
          <span class="caption">Use ldap</span>
        </label>
      </div>
    </div>
  </xsl:template>


  <xsl:template name="basic">

    <div class="grid">
      <div class="row">
        <div class="input-control text">

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
      <div class="panel" style="margin:0.75rem 0;">
        <div class="heading">
          <xsl:call-template name="browser-icon">
            <xsl:with-param name="browser" select="browser"/>
          </xsl:call-template>
          <span class="title">
            <xsl:value-of select="name"/>
            <a href="" data-profilename="{name}" data-action="deleteProfile"
               style="float:right; color:#fff; margin-right:0.75rem;">
              <span class="mif-cross"></span>
            </a>
            <a href="" data-profilename="{name}" data-action="editProfile"
               style="float:right; color:#fff; margin-right:0.75rem;">
              <span class="mif-pencil"></span>
            </a>
          </span>
        </div>
        <div class="content padding10">
          Driver options:
          <xsl:value-of select="driverOptions"/>
          <br/>
          Arguments:
          <xsl:value-of select="arguments"/>
          Capabilities:
          <xsl:value-of select="capabilities"/>
        </div>
      </div>
    </xsl:for-each>
  </xsl:template>


  <xsl:template name="browser-icon">
    <xsl:param name="browser"/>
    <xsl:variable name="browserBase" select="substring-before($browser,'_')" />

    <xsl:variable name="imageUrl">
      <xsl:choose>
        <xsl:when test="$browserBase = 'internetExplorer'"><xsl:value-of select="/data/system/baseUrl"/>css/images/ie.png</xsl:when>
        <xsl:when test="$browserBase = 'edge'"><xsl:value-of select="/data/system/baseUrl"/>css/images/edge.png</xsl:when>
        <xsl:when test="$browserBase = 'chrome'"><xsl:value-of select="/data/system/baseUrl"/>css/images/chrome.png</xsl:when>
        <xsl:when test="$browserBase = 'firefox'"><xsl:value-of select="/data/system/baseUrl"/>css/images/firefox.png</xsl:when>
        <xsl:otherwise><xsl:value-of select="/data/system/baseUrl"/>css/images/browser.png</xsl:otherwise>
      </xsl:choose>
    </xsl:variable>
    <img class="icon" src="{$imageUrl}"/>
  </xsl:template>

  <xsl:template name="dialog">
    <div data-role="dialog" id="dialog" class="padding20">
      <h4 class="dialogTitle"></h4>
      <p class="dialogContent"></p>
      <button class="button primary" id="dialogButton">Ok</button><xsl:text> </xsl:text>
      <a href="" class="button" id="dialogButtonClose">Cancel</a>
    </div>
  </xsl:template>
</xsl:stylesheet>