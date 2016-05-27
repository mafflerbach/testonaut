<xsl:stylesheet
        version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

  <xsl:template name="breadcrumb">

    <ul class="breadcrumbs2 small">
      <li>
        <a href="{/data/system/baseUrl}">
          <span class="icon mif-home"></span>
        </a>
      </li>
      <xsl:for-each select="/data/system/breadcrumb/item">
        <li>
          <a href="{/data/system/baseUrl}{path}">
            <xsl:value-of select="label"/>
          </a>
        </li>
      </xsl:for-each>
    </ul>

  </xsl:template>


  <xsl:template name="toc">
    <div class="treeview" data-role="treeview">
      <ul>
        <xsl:for-each select="/data/system/toc/*">
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
        <li class="node collapsed">
          <xsl:choose>
            <xsl:when test="/data/system/requestUri/text() != ''">
              <xsl:call-template name="link">
                <xsl:with-param name="link">
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
            <xsl:for-each select="*">
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


  <xsl:template name="application-bar">
    <div class="app-bar">
      <ul class="app-bar-menu">
        <xsl:for-each select="/data/menu/item">
          <xsl:choose>
            <xsl:when test="item">
              <li>
                <a href="" class="dropdown-toggle">
                  <xsl:value-of select="label"/>
                </a>
                <ul class="d-menu" data-role="dropdown">
                  <xsl:for-each select="item">
                    <xsl:call-template name="application-bar-item">
                      <xsl:with-param name="label" select="label"/>
                      <xsl:with-param name="badge" select="badge"/>
                      <xsl:with-param name="version" select="version"/>
                      <xsl:with-param name="path" select="path"/>
                    </xsl:call-template>
                  </xsl:for-each>
                </ul>
              </li>
            </xsl:when>
            <xsl:otherwise>
              <xsl:call-template name="application-bar-item">
                <xsl:with-param name="label" select="label"/>
                <xsl:with-param name="path" select="path"/>
              </xsl:call-template>
            </xsl:otherwise>
          </xsl:choose>

        </xsl:for-each>
      </ul>
    </div>

  </xsl:template>

  <xsl:template name="application-bar-item">
    <xsl:param name="label"/>
    <xsl:param name="path"/>
    <xsl:param name="badge" select="''"/>
    <xsl:param name="version" select="''"/>

    <li>
        <xsl:choose>
          <xsl:when test="$badge != ''">
            <a href="{/data/system/baseUrl}{$path}" class="run-test" data-browser="{$label}" data-path="{$path}" style="">
            <xsl:value-of select="$label"/>
            <xsl:value-of select="$version"/>
              <xsl:text> </xsl:text>
              <span class="badge badge-default"><xsl:value-of select="$badge"/></span>
            </a>
          </xsl:when>
          <xsl:otherwise>
            <a href="{/data/system/baseUrl}{$path}">
            <xsl:value-of select="$label"/>
            </a>
          </xsl:otherwise>
        </xsl:choose>
    </li>
  </xsl:template>


</xsl:stylesheet>