<xsl:stylesheet
        version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

  <xsl:import href="base.xsl"/>

  <xsl:template match="/">
    <xsl:text disable-output-escaping='yes'>&lt;!DOCTYPE html&gt;</xsl:text>
    <html>
      <body>
        <xsl:call-template name="content"/>
      </body>
    </html>
  </xsl:template>

  <xsl:template name="content">

    <xsl:for-each select="/data/result/item">
      <table>
        <xsl:for-each select="result/item">
          <xsl:call-template name="row">
            <xsl:with-param name="action" select="item[2]"/>
            <xsl:with-param name="command" select="item[3]"/>
          </xsl:call-template>
        </xsl:for-each>
      </table>

      <xsl:choose>
        <xsl:when test="browserResult = '1'">
          <p>Build: Success</p>
        </xsl:when>
        <xsl:otherwise>
          <p>Build: Failed
            <br/>
            In Test
            <a href="http://{/data/system/servername}{/data/system/baseUrl}{path}">
              <xsl:value-of
                      select="path"/>
            </a>
          </p>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:for-each>
  </xsl:template>


  <xsl:template name="row">
    <xsl:param name="command"/>
    <xsl:param name="action"/>
    <tr>
      <td>
        <xsl:value-of select="$command"/>
      </td>
      <td>
        <xsl:value-of select="$action"/>
      </td>
    </tr>
  </xsl:template>

</xsl:stylesheet>