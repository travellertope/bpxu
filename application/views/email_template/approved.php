<!doctype html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta charset="utf-8"> <!-- utf-8 works for most cases -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
    <meta name="x-apple-disable-message-reformatting">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
    <title><?php echo trans('account').' '.trans('approved') ?></title>
    <style>
      /* -------------------------------------
          GLOBAL RESETS
      ------------------------------------- */
      
      /*All the styling goes here*/
      
      img {
        border: none;
        -ms-interpolation-mode: bicubic;
        max-width: 100%; 
      }

      body {
        background-color: #f6f6f6;
        color: #0d0c22;
        font-family: sans-serif;
        -webkit-font-smoothing: antialiased;
        font-size: 16px;
        line-height: 1.4;
        margin: 0;
        padding: 0;
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%; 
      }

      table {
        border-collapse: separate;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
        width: 100%; }
        table td {
          font-family: sans-serif;
          font-size: 16px;
          vertical-align: top; 
      }

      /* -------------------------------------
          BODY & CONTAINER
      ------------------------------------- */

      .body {
        background-color: #f6f6f6;
        width: 100%; 
      }

      /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
      .container {
        display: block;
        margin: 0 auto !important;
        /* makes it centered */
        max-width: 580px;
        padding: 10px;
        width: 580px; 
      }

      /* This should also be a block element, so that it will fill 100% of the .container */
      .content {
        box-sizing: border-box;
        display: block;
        margin: 0 auto;
        max-width: 580px;
        padding: 10px; 
      }

      /* -------------------------------------
          HEADER, FOOTER, MAIN
      ------------------------------------- */
      .main {
        background: #ffffff;
        border-radius: 6px;
        width: 100%; 
      }

      .wrapper {
        box-sizing: border-box;
        padding: 20px; 
      }

      .content-block {
        padding-bottom: 10px;
        padding-top: 10px;
      }

      .footer {
        clear: both;
        margin-top: 10px;
        text-align: center;
        width: 100%; 
      }
        .footer td,
        .footer p,
        .footer span,
        .footer a {
          color: #999999;
        font-size: 16px;
          text-align: center; 
      }

      /* -------------------------------------
          TYPOGRAPHY
      ------------------------------------- */
      h1,
      h2,
      h3,
      h4 {
        color: #000000;
        font-family: sans-serif;
        font-weight: 500;
        line-height: 1.4;
        margin: 0;
        margin-bottom: 30px; 
      }

      h1 {
        font-size: 35px;
        font-weight: 500;
        text-align: center;
        text-transform: capitalize; 
      }

      p,
      ul,
      ol {
        font-family: sans-serif;
        font-size: 16px;
        font-weight: normal;
        margin: 0;
        margin-bottom: 15px; 
      }
        p li,
        ul li,
        ol li {
          list-style-position: inside;
          margin-left: 5px; 
      }

      a {
        color: #3498db;
        text-decoration: underline; 
      }

      /* -------------------------------------
          BUTTONS
      ------------------------------------- */
      .btn {
        box-sizing: border-box;
        width: 100%; }
        .btn > tbody > tr > td {
          padding-bottom: 15px; }
        .btn table {
          width: auto; 
      }
        .btn table td {
          background-color: #ffffff;
          border-radius: 5px;
          text-align: center; 
      }
        .btn a {
          background-color: #ffffff;
          border: solid 1px #3498db;
          border-radius: 5px;
          box-sizing: border-box;
          color: #3498db;
          cursor: pointer;
          display: inline-block;
          font-size: 16px;
          font-weight: bold;
          margin: 0;
          padding: 12px 25px;
          text-decoration: none;
          text-transform: capitalize; 
      }

      .btn-primary table td {
        background-color: #3498db; 
      }

      .btn-primary a {
        background-color: #3498db;
        border-color: #3498db;
        color: #ffffff; 
      }

      /* -------------------------------------
          OTHER STYLES THAT MIGHT BE USEFUL
      ------------------------------------- */
      .last {
        margin-bottom: 0; 
      }

      .first {
        margin-top: 0; 
      }

      .align-center {
        text-align: center; 
      }

      .align-right {
        text-align: right; 
      }

      .align-left {
        text-align: left; 
      }

      .clear {
        clear: both; 
      }

      .mt0 {
        margin-top: 0; 
      }

      .mb0 {
        margin-bottom: 0; 
      }

      .preheader {
        color: transparent;
        display: none;
        height: 0;
        max-height: 0;
        max-width: 0;
        opacity: 0;
        overflow: hidden;
        mso-hide: all;
        visibility: hidden;
        width: 0; 
      }

      .powered-by a {
        text-decoration: none; 
      }

      hr {
        border: 0;
        border-bottom: 1px solid #f6f6f6;
        margin: 20px 0; 
      }

      .bold{
        font-weight: bold;
      }

      .text-muted{
        color: #9e9ea7 !important;
      }

      .text-center{
        text-align: center;
      }

      /* -------------------------------------
          RESPONSIVE AND MOBILE FRIENDLY STYLES
      ------------------------------------- */
      @media only screen and (max-width: 620px) {
        table.body h1 {
          font-size: 28px !important;
          margin-bottom: 10px !important; 
        }
        table.body p,
        table.body ul,
        table.body ol,
        table.body td,
        table.body span,
        table.body a {
          font-size: 16px !important; 
        }
        table.body .wrapper,
        table.body .article {
          padding: 10px !important; 
        }
        table.body .content {
          padding: 0 !important; 
        }
        table.body .container {
          padding: 0 !important;
          width: 100% !important; 
        }
        table.body .main {
          border-left-width: 0 !important;
          border-radius: 0 !important;
          border-right-width: 0 !important; 
        }
        table.body .btn table {
          width: 100% !important; 
        }
        table.body .btn a {
          width: 100% !important; 
        }
        table.body .img-responsive {
          height: auto !important;
          max-width: 100% !important;
          width: auto !important; 
        }
      }

      /* -------------------------------------
          PRESERVE THESE STYLES IN THE HEAD
      ------------------------------------- */
      @media all {
        .ExternalClass {
          width: 100%; 
        }
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
          line-height: 100%; 
        }
        .apple-link a {
          color: inherit !important;
          font-family: inherit !important;
          font-size: inherit !important;
          font-weight: inherit !important;
          line-height: inherit !important;
          text-decoration: none !important; 
        }
        #MessageViewBody a {
          color: inherit;
          text-decoration: none;
          font-size: inherit;
          font-family: inherit;
          font-weight: inherit;
          line-height: inherit;
        }
        .btn-primary table td:hover {
          background-color: #007bff !important; 
        }
        .btn-primary a:hover {
          background-color: #007bff !important;
          border-color: #007bff !important; 
        } 
      }

      .border{
        border: 1px dashed #ddd;
        border-radius: 6px;
        padding: 10px 20px;
        width: 11%;
      }

      .color-primary{
        color:  #007bff;
      }

    </style>
  </head>
  <body>
    <span class="preheader"></span>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td>&nbsp;</td>
        <td class="container">
          <div class="content">

            <!-- START HEADER -->
            <div class="footer">
              <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td class="content-block powered-by">
                    <!-- <img width="90px" src="<?php //echo base_url(settings()->logo) ?>"> -->
                  </td>
                </tr>
              </table>
            </div>
            <!-- END HEADER -->

            <!-- START CENTERED WHITE CONTAINER -->
            <table role="presentation" class="main">

              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class="wrapper">
                  <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td>
                        <p class="bold"><?php echo trans('hello') ?> <?php echo html_escape($name) ?>,</p>
                        <div class="text-muteds">
We’re absolutely thrilled to have you onboard as a mentor. By joining us, you’re stepping into a role that holds the power to truly change lives. Mentorship is more than just sharing your knowledge; it’s about inspiring growth, building confidence, and guiding others to achieve their full potential.
<br/><br/>
As a mentor, you become a beacon of guidance, shaping the future by empowering others with the tools and advice they need to succeed in their careers. The ripple effect of your support, insights, and encouragement will extend far beyond the individual mentees you work with. You are shaping careers, building confidence, and helping people unlock their potential to create meaningful change in their lives and industries.
<br/><br/>
The benefits of mentorship are mutual: as you guide others, you grow too. This journey offers the chance to refine your leadership skills, build lasting professional relationships, and an opportunity for deep personal fulfilment, knowing that your experiences can help light the path for someone else.
<br/><br/>
We are honoured to have you in our community. Your impact as a mentor will be immense—and together, we will create a platform where growth, learning, and success flourish.
<br/><br/>
As you begin this journey, please remember these top tips for mentoring;
<br/><br/>
<ol>
    <li><strong>Build Trust from the Start:</strong> Establish a strong foundation of trust with your mentee. Be open, approachable, and genuinely invested in their growth. A strong relationship begins with active listening and creating a safe space for honest conversations.</li>
<li><strong>Set Clear Expectations:</strong> Early on, discuss goals and expectations—both for yourself and your mentee. What are they hoping to gain from this experience? Clarifying roles and outcomes will help both of you stay on track.</li>
<li><strong>Be a Good Listener:</strong> Mentoring is about guiding, not just instructing. Listen actively to understand your mentee’s concerns, ideas, and challenges. Often, the best advice stems from a deep understanding of their unique perspective.</li>
<li><strong>Share Your Experience, Not Just Your Success:</strong> While it’s tempting to focus on accomplishments, don’t hesitate to share your struggles and failures too. Mentees learn just as much, if not more, from hearing about the mistakes and challenges that helped shape your journey.</li>
<li><strong>Provide Constructive Feedback:</strong> Honest and constructive feedback is crucial for growth. Be encouraging, but also don’t shy away from pointing out areas for improvement. Frame your feedback in a way that is both supportive and actionable.</li>
<li><strong>Celebrate Wins, Big and Small:</strong> Acknowledge your mentee’s progress, no matter how small. Celebrating achievements boosts their confidence and reinforces the positive impact of your guidance.</li>
</ol>
<br/><br/>
By following these principles, you’ll create a positive and empowering mentoring experience that fosters growth, confidence, and long-term success. Your guidance can unlock doors that lead to new opportunities for your mentee, while also enriching your own journey as a leader and learner. I hope you find this experience truly rewarding, and I thank you for all your good work.
<br/><br/>
Thank you for your commitment to making a difference. Let’s build something incredible together!
<br/><br/>
Welcome to the movement of shaping lives for the better; <a href="https://pairedbybpu.uk/login">click here to log in</a> to the website and continue for more information. <br/><br/>
– Enoch Adeyemi
<br/><br/>
</div>
                        <p>
                          <?php echo settings()->site_name ?><br>
                          <?php echo settings()->admin_email ?>
                        </p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

            <!-- END MAIN CONTENT AREA -->
            </table>
            <!-- END CENTERED WHITE CONTAINER -->

            <!-- START FOOTER -->
            <div class="footer">
              <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td class="content-block powered-by">
                    <?php echo trans('powered-by') ?> <a href="<?php echo base_url() ?>" target="_blank"><?php echo settings()->site_name ?></a>
                  </td>
                </tr>
              </table>
            </div>
            <!-- END FOOTER -->

          </div>
        </td>
        <td>&nbsp;</td>
      </tr>
    </table>
  </body>
</html>