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
    <xsl:call-template name="browser">
      <xsl:with-param name="browser" select="/data/history/*"/>
    </xsl:call-template>
  </xsl:template>

  <xsl:template name="browser">
    <xsl:param name="browser"/>

    <xsl:for-each select="$browser">

      <div class="panel" data-role="panel" >
        <div class="heading">
          <span class="title">
            <xsl:value-of select="name($browser)"/>
          </span>
        </div>
        <div class="content">
          <xsl:call-template name="page">
            <xsl:with-param name="page" select="$browser/item"/>
          </xsl:call-template>
        </div>
      </div>
    </xsl:for-each>
  </xsl:template>

  <xsl:template name="page">
    <xsl:param name="page"/>

    <xsl:for-each select="$page">
      <div class="panel collapsed" style="padding:0 10px;" data-role="panel">
        <div class="heading">
          <span class="title">
            <xsl:value-of select="$page/@name"/>
          </span>
        </div>
        <div class="content">
          <xsl:call-template name="runs">
            <xsl:with-param name="run" select="$page/item"/>
          </xsl:call-template>
        </div>
      </div>
    </xsl:for-each>

  </xsl:template>


  <xsl:template name="runs">
    <xsl:param name="run"/>
    <xsl:for-each select="$run">
      <xsl:variable name="cssClass">
        <xsl:choose>
          <xsl:when test="$run/run/item[1] = '1'">success</xsl:when>
          <xsl:otherwise>alert</xsl:otherwise>
        </xsl:choose>
      </xsl:variable>

      <div class="panel collapsed {$cssClass}" data-role="panel">
        <div class="heading">
          <span class="title">
            <xsl:value-of select="date"/><xsl:text> </xsl:text>
            <xsl:value-of select="time"/>
          </span>
        </div>
        <div class="content" style="margin:0 10px;">
          <xsl:call-template name="command-list">
            <xsl:with-param name="run" select="run"/>
          </xsl:call-template>
        </div>
      </div>
    </xsl:for-each>
  </xsl:template>


  <xsl:template name="command-list">
    <xsl:param name="run"/>
    <table class="table no-margin">
      <xsl:for-each select="$run/item">
        <xsl:variable name="cssClass">
          <xsl:choose>
            <xsl:when test="item[1] = '1'">success</xsl:when>
            <xsl:otherwise>error</xsl:otherwise>
          </xsl:choose>
        </xsl:variable>
        <tr class="{$cssClass}">
          <td>
            <xsl:value-of select="item[2]"/>
          </td>
          <td>
            <xsl:value-of select="item[3]"/>
          </td>
        </tr>
      </xsl:for-each>
    </table>
  </xsl:template>


</xsl:stylesheet>