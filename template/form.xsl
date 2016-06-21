<xsl:stylesheet
        version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

  <xsl:template name="login-form">

    <div class="login-form padding20 block-shadow">
      <form action="" method="POST">
        <h1 class="text-light">Login to testonaut</h1>
        <hr class="thin"/>
        <br/>
        <div class="input-control text full-size" data-role="input">
          <label for="user_login">User email:</label>
          <input type="text" name="username" id="user_login"/>
          <button class="button helper-button clear">
            <span class="mif-cross"></span>
          </button>
        </div>
        <br/>
        <br/>
        <div class="input-control password full-size" data-role="input">
          <label for="user_password">User password:</label>
          <input type="password" name="password" id="user_password"/>
          <button class="button helper-button reveal">
            <span class="mif-looks"></span>
          </button>
        </div>
        <br/>
        <br/>
        <div class="form-actions">
          <button type="submit" class="button primary">Login to</button>
          <a href="{/data/system/baseUrl}" class="button link">Cancel</a>
          <a href="{/data/system/baseUrl}reset/" style="position: absolute; right: 5px; top: 5px;">Forget Password?</a>
          <a href="{/data/system/baseUrl}register" style="position: absolute; right: 5px; top: 23px;">Register?</a>
        </div>
      </form>
    </div>


    <xsl:call-template name="message">
      <xsl:with-param name="title">Login</xsl:with-param>
    </xsl:call-template>

    <script type="text/javascript">

      $(function(){
      var form = $(".login-form");
      form.css({
      opacity: 1,
      "-webkit-transform": "scale(1)",
      "transform": "scale(1)",
      "-webkit-transition": ".5s",
      "transition": ".5s"
      });
      });
    </script>
  </xsl:template>

  <xsl:template name="reset-form">

    <div class="reset-form padding20 block-shadow">
      <form action="" method="POST">
        <h1 class="text-light">Reset Password</h1>
        <hr class="thin"/>
        <br/>
        <div class="input-control text full-size" data-role="input">
          <label for="user_login">User email:</label>
          <input type="text" name="email" placeholder=""/>
          <button class="button helper-button clear">
            <span class="mif-cross"></span>
          </button>
        </div>
        <br/>
        <br/>
        <div class="form-actions">
          <button type="submit" class="button primary">Reset</button>
          <a href="{/data/system/baseUrl}" class="button link">Cancel</a>
        </div>
        <input type="hidden" name="action" value="reset"/>
      </form>
    </div>

    <xsl:call-template name="message">
      <xsl:with-param name="title">Reset</xsl:with-param>
    </xsl:call-template>

    <script type="text/javascript">

      $(function(){
      var form = $(".reset-form");
      form.css({
      opacity: 1,
      "-webkit-transform": "scale(1)",
      "transform": "scale(1)",
      "-webkit-transition": ".5s",
      "transition": ".5s"
      });
      });
    </script>
  </xsl:template>

  <xsl:template name="register-form">

    <div class="register-form padding20 block-shadow">
      <form action="" method="POST">
        <h1 class="text-light">Register</h1>
        <hr class="thin"/>
        <br/>
        <div class="input-control text full-size" data-role="input">
          <label for="user_login">User email:</label>
          <input type="text" name="email" id="user_login"/>
          <button class="button helper-button clear">
            <span class="mif-cross"></span>
          </button>
        </div>
        <br/>
        <br/>
        <div class="input-control password full-size" data-role="input">
          <label for="user_password">User password:</label>
          <input type="password" name="password" id="user_password"/>
          <button class="button helper-button reveal">
            <span class="mif-looks"></span>
          </button>
        </div>
        <br/>
        <br/>
        <div class="input-control text full-size" data-role="input">
          <label for="displayName">Displayname:</label>
          <input type="text" name="displayName" id="displayName"/>
          <button class="button helper-button clear">
            <span class="mif-cross"></span>
          </button>
        </div>
        <br/>
        <br/>
        <div class="form-actions">
          <button type="submit" class="button primary">Register</button>
          <a href="{/data/system/baseUrl}" class="button link">Cancel</a>
        </div>
      </form>
    </div>

    <xsl:call-template name="message">
      <xsl:with-param name="title">Register</xsl:with-param>
    </xsl:call-template>

    <script type="text/javascript">

      $(function(){
      var form = $(".register-form ");
      form.css({
      opacity: 1,
      "-webkit-transform": "scale(1)",
      "transform": "scale(1)",
      "-webkit-transition": ".5s",
      "transition": ".5s"
      });
      });
    </script>
  </xsl:template>

  <xsl:template name="message">
    <xsl:param name="title"/>
    <xsl:if test="/data/message/text()">

      <div id="dialog"
           class="padding20"
           data-role="dialog"
           data-close-button="true"
           data-overlay="true"
           data-overlay-click-close="true"
           data-overlay-color="op-dark">
        <h3><xsl:value-of select="$title"/></h3>
        <p>
          <xsl:value-of select="/data/message"/>
        </p>
      </div>

      <script>
        var dialog = $('#dialog').data('dialog');
        dialog.open();

      </script>

    </xsl:if>

  </xsl:template>


  <xsl:template match="input" mode="text">
    <input type="text" placeholder=""/>
  </xsl:template>

  <xsl:template match="input" mode="password">
    <input type="password" placeholder=""/>
  </xsl:template>

</xsl:stylesheet>