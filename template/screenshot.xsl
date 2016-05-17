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
          <div class="row">
            <div class="cell colspan12">
              <xsl:call-template name="filter"/>
            </div>
          </div>
          <div class="row">
            <div class="cell colspan12">
              <xsl:call-template name="content"/>
            </div>
          </div>
        </div>

        <xsl:call-template name="window-dialog"/>
        <script type="text/javascript">
          initScreenshots();
        </script>
      </body>
    </html>
  </xsl:template>

  <xsl:template name="filter">

    <h5>
      Filter:

      <button class="button primary" id="all">All</button>
      <xsl:text> </xsl:text>
      <button class="button success" id="success">Success</button>
      <xsl:text> </xsl:text>
      <button class="button danger" id="fail">Failed</button>
    </h5>

  </xsl:template>


  <xsl:template name="content">
    <xsl:for-each select="/data/images/*">
      <xsl:call-template name="panel">
        <xsl:with-param name="browser" select="."></xsl:with-param>
      </xsl:call-template>
    </xsl:for-each>

  </xsl:template>


  <xsl:template name="panel">
    <xsl:param name="browser"/>
    <div class="panel collapsible" data-role="panel">
      <div class="heading">
        <span class="title">
          <xsl:value-of select="name($browser)"/>
        </span>
      </div>
      <div class="content padding10">

        <xsl:for-each select="$browser/*">

          <div class="panel collapsible" data-role="panel">
            <div class="heading">
              <span class="title">
                <xsl:value-of select="name(.)"/>
              </span>
            </div>
            <div class="content padding10">
              <xsl:call-template name="screenshot-line">
                <xsl:with-param name="webpath" select="webpath"/>
              </xsl:call-template>
            </div>
          </div>
        </xsl:for-each>

      </div>
    </div>

  </xsl:template>


  <xsl:template name="screenshot-line">
    <xsl:param name="webpath"/>
    <div class="flex-grid">

      <xsl:for-each select="$webpath/item">
        <xsl:call-template name="image">
          <xsl:with-param name="image" select="."/>
        </xsl:call-template>
      </xsl:for-each>
    </div>
  </xsl:template>


  <xsl:template name="image">
    <xsl:param name="image"/>
    <xsl:variable name="cssClass">
      <xsl:choose>
        <xsl:when test="$image/result = '1'">
          success
        </xsl:when>
        <xsl:otherwise>
          alert
        </xsl:otherwise>
      </xsl:choose>
    </xsl:variable>

    <div class="panel imagePanel {$cssClass}">
      <div class="heading">
        <span class="title">
          <xsl:value-of select="$image/imageName"/>
        </span>
      </div>
      <div class="content padding10">
        <div class="row">
          <div class="cell size4" style="margin-right:10px;margin-left:-10px;">
            <div class="image-container image-format-square" style="width: 100%;">
              <div class="frame">
                <div style="width: 100%; height: 300px; border-radius: 0px; background-image: url('{$image/item[1]}'); background-size: cover; background-repeat: no-repeat;"></div>
              </div>
              <div class="image-overlay op-green">

                <a class="copyImage" style="position: relative; z-index: 3"
                   href="{/data/system/baseUrl}screenshot/copy/{name(../../..)}/{$image/imageName}/{/data/path}">
                  <span class="copy mif-checkmark mif-4x"
                        data-link="{/data/system/baseUrl}screenshot/copy/{name(../../..)}/{$image/imageName}/{/data/path}">
                  </span>
                </a>
                <a class="deleteImage" style="position: relative; z-index: 3"
                   href="{/data/system/baseUrl}screenshot/delete/src/{name(../../..)}/{$image/imageName}/{/data/path}">
                  <span class="delete mif-cross mif-4x"
                        data-link="{/data/system/baseUrl}screenshot/delete/src/{name(../../..)}/{$image/imageName}/{/data/path}">
                  </span>
                </a>

              </div>
            </div>
          </div>

          <div class="cell size4">
            <div class="image-container image-format-square" style="width: 100%;">
              <div class="frame">
                <div style="width: 100%; height: 300px; border-radius: 0px; background-image: url('{$image/item[2]}'); background-size: cover; background-repeat: no-repeat;"></div>
              </div>
              <div class="image-overlay op-green">

                <a class="deleteImage" style="position: relative; z-index: 3"
                   href="{/data/system/baseUrl}screenshot/delete/ref/{name(../../..)}/{$image/imageName}/{/data/path}">
                  <span class="delete mif-cross mif-4x"
                        data-link="{/data/system/baseUrl}/screenshot/delete/ref/{name(../../..)}/{$image/imageName}/{/data/path}">
                  </span>
                </a>
              </div>
            </div>
          </div>

          <div class="cell size4" style="margin-left:10px; margin-right:-10px;">
            <div class="image-container image-format-square" style="width: 100%;">
              <div class="frame">
                <div style="width: 100%; height: 300px; border-radius: 0px; background-image: url('{$image/item[3]}'); background-size: cover; background-repeat: no-repeat;"></div>
              </div>
              <div class="image-overlay op-green">
                <a class="deleteImage" style="position: relative; z-index: 3"
                   href="{/data/system/baseUrl}screenshot/delete/comp/{name(../../..)}/{$image/imageName}/{/data/path}">
                  <span class="delete mif-cross mif-4x"
                        data-link="{/data/system/baseUrl}/screenshot/delete/comp/{name(../../..)}/{$image/imageName}/{/data/path}">
                  </span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </xsl:template>
<xsl:template name="window-dialog">
  <div class="window">
    <div class="window-caption">
      <span class="window-caption-icon"></span>
      <span class="window-caption-title"></span>
      <span class="btn-close"></span>
    </div>
    <div class="window-content" style="height: 100px">
      <div class="message padding10">
      </div>
      <div class="window-buttons padding10">
        <button class="button primary ok">OK</button><xsl:text> </xsl:text>
        <button class="button cancel">Cancel</button>
      </div>
    </div>
  </div>
</xsl:template>



</xsl:stylesheet>