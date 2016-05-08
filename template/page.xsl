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

            <xsl:call-template name="breadcrumb"/>
          </div>
          <div class="row">
            <div class="cell colspan2">
              <xsl:call-template name="toc"/>
            </div>
            <div class="cell colspan10">
              <xsl:call-template name="content"/>
            </div>
          </div>
        </div>


      </body>
    </html>
  </xsl:template>

  <xsl:template name="content">
    <xsl:value-of select="/data/content" disable-output-escaping="yes"/>

  </xsl:template>

  <xsl:template name="breadcrumb">

    <ul class="breadcrumbs2 small">
      <li>
        <a href="{/data/system/baseUrl}">
          <span class="icon mif-home"></span>
        </a>
      </li>
      <xsl:for-each select="/data/system/breadcrumb/item">
        <li>
          <a href="{path}">
            <xsl:value-of select="label"/>
          </a>
        </li>
      </xsl:for-each>
    </ul>

  </xsl:template>


  <xsl:template name="toc">
    <div class="treeview" data-role="treeview">
      <ul>
        <xsl:for-each select="/data/system/toc/item">
          <xsl:call-template name="list-item">
            <xsl:with-param name="style" select="'node'"/>
            <xsl:with-param name="label" select="@name"/>
            <xsl:with-param name="link" select="@name"/>
          </xsl:call-template>
        </xsl:for-each>
      </ul>
    </div>
  </xsl:template>


  <xsl:template name="list-item">
    <xsl:param name="label"/>
    <xsl:param name="link"/>
    <xsl:param name="style"/>

    <xsl:choose>
      <xsl:when test="item">
        <li class="node">
          <xsl:choose>
            <xsl:when test="/data/system/requestUri/text() != ''">
              <xsl:call-template name="link">
                <xsl:with-param name="link">
                  <xsl:value-of select="/data/system/requestUri"/>
                  <xsl:value-of select="$link"/>
                </xsl:with-param>
                <xsl:with-param name="label" select="$label"/>
              </xsl:call-template>
            </xsl:when>
            <xsl:otherwise>
              <xsl:call-template name="link">
                <xsl:with-param name="link">
                  <xsl:value-of select="$link"/>
                </xsl:with-param>
                <xsl:with-param name="label" select="$label"/>
              </xsl:call-template>
            </xsl:otherwise>
          </xsl:choose>

          <span class="node-toggle"></span>
          <ul>
            <xsl:for-each select="item">
              <xsl:call-template name="list-item">
                <xsl:with-param name="link">
                  <xsl:value-of select="$link"/>.<xsl:value-of select="@name"/>
                </xsl:with-param>
                <xsl:with-param name="label" select="@name"/>
                <xsl:with-param name="style" select="''"/>
              </xsl:call-template>
            </xsl:for-each>
          </ul>
        </li>
      </xsl:when>
      <xsl:otherwise>
        <li class="{$style}">
          <span class="leaf">
            <xsl:call-template name="link">
              <xsl:with-param name="link" select="$link"/>
              <xsl:with-param name="label" select="$label"/>
            </xsl:call-template>
          </span>
        </li>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="link">
    <xsl:param name="label"/>
    <xsl:param name="link"/>

    <a class="leaf">
      <xsl:choose>
        <xsl:when test="/data/system/requestUri/text() != ''">
          <xsl:attribute name="href">
            <xsl:value-of select="/data/system/requestUri"/>.<xsl:value-of select="$link"/>
          </xsl:attribute>
        </xsl:when>
        <xsl:otherwise>
          <xsl:attribute name="href">
            <xsl:value-of select="$link"/>
          </xsl:attribute>
        </xsl:otherwise>
      </xsl:choose>
      <xsl:value-of select="$label"/>
    </a>

  </xsl:template>


</xsl:stylesheet>