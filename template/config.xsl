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
            <xsl:call-template name="breadcrumb"/>
          </div>
          <div class="row auto-size ">
            <div class="cell size-p100 padding20">
              <xsl:call-template name="content"/>
            </div>
          </div>
        </div>
        <script type="text/javascript">
          initConfig();
        </script>
      </body>
    </html>
  </xsl:template>

  <xsl:template name="content">
    <form action="" method="POST">
      <xsl:call-template name="panel">
        <xsl:with-param name="title" select="'Page Setting'"/>
        <xsl:with-param name="content">
          <xsl:call-template name="radio">
            <xsl:with-param name="name" select="'pagesettings'"/>
            <xsl:with-param name="list" select="/data/pagesettings"/>
          </xsl:call-template>
        </xsl:with-param>
      </xsl:call-template>

      <br/>
      <xsl:call-template name="panel">
        <xsl:with-param name="title" select="'Screenshot Setting'"/>
        <xsl:with-param name="content">
          <xsl:call-template name="radio">
            <xsl:with-param name="name" select="'screenshotsettings'"/>
            <xsl:with-param name="list" select="/data/screenshotsettings"/>
          </xsl:call-template>
        </xsl:with-param>
      </xsl:call-template>
      <br/>
      <xsl:call-template name="browser-panel-list"/>
      <br/>
      <xsl:call-template name="originUrl"/>


      <br/>
      <input type="hidden" name="action" value="save"/>
      <xsl:text> </xsl:text>
      <input type="submit" name="save" value="Save" class="button primary"/>
      <xsl:text> </xsl:text>
      <a href="{/data/system/baseUrl}{/data/page/path}" class="button">Cancel</a>
    </form>
  </xsl:template>

  <xsl:template name="panel">
    <xsl:param name="title"/>
    <xsl:param name="content"/>

    <div class="panel">
      <div class="heading">
        <span class="title">
          <xsl:value-of select="$title"/>
        </span>
      </div>
      <div class="content">
        <xsl:copy-of select="$content"/>
      </div>
    </div>

  </xsl:template>

  <xsl:template name="originUrl">

    <xsl:if test="/data/originUrl">
      <xsl:call-template name="panel">
        <xsl:with-param name="title" select="'Origin Url'"/>
        <xsl:with-param name="content">
          <div class="input-control text margin10 ">
            <input type="text" name="originUrl" value="{/data/originUrl}" placeholder="origin Url"/>
          </div>
        </xsl:with-param>
      </xsl:call-template>
    </xsl:if>


  </xsl:template>


  <xsl:template name="browser-panel-list">
    <xsl:if test="/data/browser">
      <xsl:call-template name="panel">
        <xsl:with-param name="title" select="'Test Urls'"/>
        <xsl:with-param name="content">
          <xsl:for-each select="/data/browser/item">
            <xsl:call-template name="browser-panel">
              <xsl:with-param name="browsername" select="browserName"/>
              <xsl:with-param name="url" select="url"/>
              <xsl:with-param name="os" select="platform"/>
              <xsl:with-param name="version" select="version"/>
              <xsl:with-param name="active" select="active"/>
            </xsl:call-template>
          </xsl:for-each>
        </xsl:with-param>
      </xsl:call-template>
    </xsl:if>
  </xsl:template>

  <xsl:template name="browser-panel">
    <xsl:param name="browsername"/>
    <xsl:param name="os"/>
    <xsl:param name="url"/>
    <xsl:param name="version"/>
    <xsl:param name="active"/>


    <div class="panel">
      <div class="heading">
        <xsl:call-template name="browser-icon">
          <xsl:with-param name="browser" select="$browsername"/>
        </xsl:call-template>
        <span class="title">
          <xsl:value-of select="$browsername"/><xsl:text> </xsl:text>
          <xsl:value-of select="$version"/><xsl:text> </xsl:text>
          (<xsl:value-of select="$os"/>)
        </span>

      </div>
      <div class="content padding10">
        <div class="input-control text">
          <input type="text" placeholder="Testsystem Url"
                 name="browser[{$os}_{$browsername}_{$version}]{$os}_{$browsername}_{$version}_Url">
            <xsl:attribute name="value">
              <xsl:value-of select="$url"/>
            </xsl:attribute>
          </input>
        </div>
        <label class="input-control checkbox small-check">
          <xsl:choose>
            <xsl:when test="$active">
              <input type="checkbox" name="active[]{$os}_{$browsername}_{$version}"
                     value="{$os}_{$browsername}_{$version}" checked="checked"/>
            </xsl:when>
            <xsl:otherwise>
              <input type="checkbox" name="active[]{$os}_{$browsername}_{$version}"
                     value="{$os}_{$browsername}_{$version}"/>
            </xsl:otherwise>
          </xsl:choose>
          <span class="check"></span>
        </label>
      </div>
    </div>

  </xsl:template>

  <xsl:template name="browser-icon">
    <xsl:param name="browser"/>
    <xsl:variable name="imageUrl">
      <xsl:choose>
        <xsl:when test="$browser = 'internetExplorer'"><xsl:value-of select="/data/system/baseUrl"/>css/images/ie.png</xsl:when>
        <xsl:when test="$browser = 'edge'"><xsl:value-of select="/data/system/baseUrl"/>css/images/edge.png</xsl:when>
        <xsl:when test="$browser = 'chrome'"><xsl:value-of select="/data/system/baseUrl"/>css/images/chrome.png</xsl:when>
        <xsl:when test="$browser = 'firefox'"><xsl:value-of select="/data/system/baseUrl"/>css/images/firefox.png</xsl:when>
        <xsl:otherwise><xsl:value-of select="/data/system/baseUrl"/>css/images/browser.png</xsl:otherwise>
      </xsl:choose>
    </xsl:variable>
    <img class="icon" src="{$imageUrl}"/>
  </xsl:template>


  <xsl:template name="radio">
    <xsl:param name="list"/>
    <xsl:param name="name"/>

    <xsl:for-each select="$list/*">
      <label class="input-control radio small-check padding10 no-padding-top no-padding-bottom">
        <xsl:choose>
          <xsl:when test=". = '1'">
            <input type="radio" name="{$name}" value="{name(.)}" checked="checked"/>
          </xsl:when>
          <xsl:otherwise>
            <input type="radio" name="{$name}" value="{name(.)}"/>
          </xsl:otherwise>
        </xsl:choose>
        <span class="check"></span>
        <span class="caption">
          <xsl:value-of select="name(.)"/>
        </span>
      </label>
      <xsl:text> </xsl:text>
      <br/>
    </xsl:for-each>
  </xsl:template>

</xsl:stylesheet>