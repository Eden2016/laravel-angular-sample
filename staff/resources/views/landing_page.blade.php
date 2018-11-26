<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ESPORTSCONSTRUCT">
    <meta name="author" content="ESPORTSCONSTRUCT author">

    <title>EsportsConstruct: Technology and Content solutions for the Esports industry</title>

    <link href="img/favicon.ico" rel="icon">

    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src='https://www.google.com/recaptcha/api.js'></script>

    <script>
        (function(i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function() {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-69399047-3', 'auto');
        ga('send', 'pageview');

    </script>
</head>

<body id="land-page">


    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="cont">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
                    <a class="navbar-brand" id="home-link"><img src="img/logo.png" alt="logo"></a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li><a id="services-link" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Main Navigation', 'eventAction': 'Nav Clicked', 'eventLabel': 'Services' });">SERVICES</a></li>
                        <li><a id="career-link" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Main Navigation', 'eventAction': 'Nav Clicked', 'eventLabel': 'Career' });">CAREER</a></li>
                        <li><a id="contact-link" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Main Navigation', 'eventAction': 'Nav Clicked', 'eventLabel': 'Contact' });">CONTACT</a></li>
                        <li><a data-toggle="modal" data-target="#imprint" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Main Navigation', 'eventAction': 'Imprint Dialog Opened', 'eventLabel': 'Imprint' });">IMPRINT</a>
                            <li>
                    </ul>

                    <div class="nav navbar-nav navbar-right">
                        <button class="ec-def-btn" data-target="#login-modal" data-toggle="modal"><span>CLIENT LOGIN</span></button>
                        <button class="ec-def-btn" data-target="#login-modal" data-toggle="modal"><span>INVESTOR LOGIN</span></button>
                    </div>
                </div>
                <!-- navbar-collapse -->
            </div>
            <!-- container-fluid -->
        </div>
    </nav>
    <!-- container -->
    <div class="jumbo">
        <h1>YOUR ESPORTS CONSTRUCTOR</h1>
        <h2>EsportsConstruct is a team of developers with a genuine<br />
passion for developing esports solutions.</h2>
        <button><span>Get in touch</span></button>
    </div>
    <!-- jumbotron -->
    <div class="our-services">
        <div class="cont">
            <div class="title-box">
                <h3>our services</h3>
                <p>EsportsConstruct offers a wide range of technology and services as well as know-how to execute your Esports vision. Selected focus solutions we provide are:</p>
            </div>
            <!-- title box -->
        </div>
        <!-- cont -->
        <div class="service-box-cont">
            <div class="service-box">
                <div><img src="img/esports-consultancy-icon.png" alt="consult"></div>
                <h3>Esports Consultancy</h3>
                <p>EsportsConstruct offers strategic advice through it’s sister company of Schmedeshagen Consulting who leverage years of experience in building successful services for Esports audiences.</p>
                <a data-target="#esports-consultancy" data-toggle="modal" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'Esports Consultancy' });">READ MORE</a>
            </div>
            <!-- service box -->
            <div class="service-box">
                <div><img src="img/data-solutions-icon.png" alt="data"></div>
                <h3>Data Solutions</h3>
                <p>EsportsConstruct data solutions help you understand the Esports landscape both on a macro and micro level with the help of highly customizeable information tools, all also available as live APIs.</p>
                <a data-toggle="modal" data-target="#data-solutions" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'Data Solutions' });">READ MORE</a>
            </div>
            <!-- service box -->
            <div class="service-box">
                <div><img src="img/content-tech-icon.png" alt="content"></div>
                <h3>Content-Technology</h3>
                <p>Content at heart, EsportsConstruct builds workflow-optimized content management systems and provides clients with their proprietary technology via versatile interfaces and feeds to supply their own products and services at minimum maintenance overhead.</p>
                <a data-dismiss="modal" data-toggle="modal" data-target="#content-technology" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'Content Technology' });">READ MORE</a>
            </div>
            <!-- service box -->
            <div class="service-box">
                <div><img src="img/ui-icon.png" alt="ui"></div>
                <h3>User Interface and Experience</h3>
                <p>Having built industry-leading interfaces for both enterprise and consumer-centric services before, the team of EsportsConstruct utilizes its know-how to create usability-focused and appealing visuals for clients.</p>
                <a data-dismiss="modal" data-toggle="modal" data-target="#user-interface" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'User Interface And Experience' });">READ MORE</a>
            </div>
            <!-- service box -->
            <div class="service-box">
                <div><img src="img/frontend-tech-icon.png" alt="front-end"></div>
                <h3>Front-End Technology</h3>
                <p>EsportsConstruct offers execution of ideas and designs leveraging its team of software engineers to support clients with turn-key solutions.</p>
                <a data-dismiss="modal" data-toggle="modal" data-target="#front-end" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'Front End Technology' });">READ MORE</a>
            </div>
            <!-- service box -->
            <div class="service-box">
                <div><img src="img/backend-tech-icon.png" alt="back end"></div>
                <h3>Back-End Technology</h3>
                <p>From cloud applications to full-stack server and cluster management, EsportsConstruct manages all aspects of technology in-house and offers clients to take advantage of such managed solutions.</p>
                <a data-dismiss="modal" data-toggle="modal" data-target="#back-end" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'Back End Technology' });">READ MORE</a>
            </div>
            <!-- service box -->
            <div class="service-box">
                <div><img src="img/marketing-icon.png" alt="marketing"></div>
                <h3>Marketing Solutions</h3>
                <p>EsportsConstruct leverages its teams vast background in Esports content creation to help clients provide plug-and-play solutions to their audiences by not just building the services needed, but also by filling these frames with content created by experts in their respective realms.</p>
                <a data-dismiss="modal" data-toggle="modal" data-target="#marketing-solutions" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'Marketing Solutions' });">READ MORE</a>
            </div>
            <!-- service box -->

        </div>
        <!-- service box cont -->
    </div>
    <!-- our services -->
    <div class="careers">
        <div class="cont">
            <div class="title-box">
                <h3>careers</h3>
                <p>Keep an eye out on this section. We are constantly looking for new staff for our team.</p>
            </div>
            <!-- title box -->
            <div class="arrows-cont">
                <a class="slider-arrows" id="slider-arr-right"><img src="img/slider-arrow-left.png" alt="arrow"></a>
                <a class="slider-arrows" id="slider-arr-left"><img src="img/slider-arrow-right.png" alt="arrow"></a>
                <div class="car-slider-cont">
                    <div class="car-slider">
                        <div class="car-slider-item">

                            <h2>Senior UI/UX Designer</h2>

                            <p>We are looking for an experienced UI/UX Designer with a passion for Esports who helps us visualize concepts of all stages for our commercial clients as well as for our internal systems. The job will focus on structuring the team’s input, mapping it against client requirements and assisting the sales and development teams from initial sketches to wireframe models, prototypes up to ultimately taking responsibility for the visuals of the work we ship. </p>

                            <p>Please combine all application collaterals into a single PDF file and include information about your preferred salary, scans for diplomas/degrees proofs, extensive detail on your portfolio as well as all letters of recommendation from previous positions.</p>

                            <p>Responsibilities:</p>
                            <ul>
                                <li>- Structure and visualize concepts and act as person of contact for the team on all things front-end development</li>
                                <li>- Take ownership for our product visuals and give direction and vision for the development team in this domain</li>
                                <li>- Collaborate closely with Project Management and Development teams to ensure smooth processes from sketch to products ready for approval</li>
                                <li>- Suggest improvements for our existing products and work in progress</li>

                            </ul>
                            <p>Qualifications:</p>
                            <ul>
                                <li>- Available to hit the ground running and willing to relocate to Germany on short notice</li>
                                <li>- Apprenticeship in media/communications design, BS/MS degree in design or relevant related</li>
                                <li>- Strong understanding of rapid prototyping and relevant tools to optimize workflow</li>
                                <li>- 5 years + of commercial experience successfully shipping products </li>
                                <li>- Exceptional written communication skills</li>
                                <li>- A passion for Esports</li>
                            </ul>

                            <a href="mailto:career@esportsconstruct.com" class="pull-right">APPLY NOW</a>

                        </div>
                        <!-- car slider item -->
                        <div class="car-slider-item">

                            <h2>Senior Data Analyst</h2>

                            <p>We are looking for an data analyst with a passion for Esports. The job will evolve around modelling historical and real-time data and build understanding for individual chance of event for plentiful in-game events as well as to identify and map scenario patterns and their likeliness of happening according to said events. </p>

                            <p>Please combine all application collaterals into a single PDF file and include information about your preferred salary, scans for diplomas/degrees proofs as well as all letters of recommendation from previous positions.</p>

                            <p>Responsibilities:</p>
                            <ul>
                                <li>- Take a leading position in modelling existing data and create new models to optimize data output</li>
                                <li>- Suggest new data points to collect info for and collaborate with Product Management to find smart ways for doing so</li>
                                <li>- Conceptualize a test environment that allows for rapid prototyping of new data analysis models and guide the development teams in building such tools</li>

                            </ul>
                            <p>Qualifications:</p>
                            <ul>
                                <li>- BS/MS degree in engineering, mathematics, IT or a related field</li>
                                <li>- A passion for Esports, game-related terminology and which results of data modelling are of interest to Esports audiences</li>
                                <li>- Strong understanding of SQL data and ability to contribute to data structure and modelling</li>
                                <li>- 3 years + as a data analyst within a digital environment</li>
                                <li>- Exceptional written communication skills</li>
                            </ul>

                            <a href="mailto:career@esportsconstruct.com" class="pull-right">APPLY NOW</a>

                        </div>
                        <!-- car slider item -->
                        <div class="car-slider-item">
                            <h2>Mobile iOS Developer</h2>
                            <p>We are looking for an experienced mobile developer with focus on building native apps for iOS devices and who have shipped at least two commercially successful applications and are able to proof their participation in such projects.</p>
                            <p>Please combine all application collaterals into a single PDF file and include information about your preferred salary, scans for diplomas/degrees proofs as well as all letters of recommendation from previous positions. Please also add a zip container with selected code you want to share for us to learn more about your skills.</p>

                            <p>Responsibilities:</p>
                            </p>
                            <ul>
                                <li>- Collaboration with Product Management for both internal and external stakeholder projects</li>
                                <li>- Develop a wide range of applications based on designs provided and integrate existing backend technology</li>
                                <li>- Help with optimizing our technology for mobile application usage purposes and maintain solutions put in place</li>
                                <li>- Identify best practices/tools for evolutive and maintainable apps and put them to use</li>
                                <li>- Contribute to continuous improvement of our internal QA, development and deployment processes</li>

                            </ul>

                            <p>Qualifications:</p>
                            <ul>
                                <li>- Experience as an integral part for at least two mobile app in the AppStore</li>
                                <li>- Comfortable with extensive object oriented coding</li>
                                <li>- Great in documenting and commenting code written</li>
                                <li>- Vast experience with Swift / Objective-C, IOS mobile frameworks</li>
                                <li>- Experience with version control solutions and test-driven development</li>
                                <li>- Experience with multi-tenant development environments</li>
                                <li>- Fluent in English, preferably at least basic understanding of German</li>

                            </ul>

                            <a href="mailto:career@esportsconstruct.com" class="pull-right">APPLY NOW</a>

                        </div>
                        <!-- car slider item -->
                        <div class="car-slider-item">
                            <h2>Mobile Android Developer</h2>
                            <p>We are looking for an experienced mobile developer with focus on building native apps for Android devices and who have shipped at least two commercially successful applications and are able to proof their participation in such projects.</p>
                            <p>Please combine all application collaterals into a single PDF file and include information about your preferred salary, scans for diplomas/degrees proofs as well as all letters of recommendation from previous positions. Please also add a zip container with selected code you want to share for us to learn more about your skills.</p>
                            <p>Responsibility: </p>

                            <ul>
                                <li>- Collaboration with Product Management for both internal and external stakeholder projects</li>
                                <li>- Develop a wide range of applications based on designs provided and integrate existing backend technology</li>
                                <li>- Help with optimizing our technology for mobile application usage purposes and maintain solutions put in place</li>
                                <li>- Identify best practices/tools for evolutive and maintainable apps and put them to use</li>
                                <li>- Contribute to continuous improvement of our internal QA, development and deployment processes</li>

                            </ul>
                            <p>Qualifications:</p>
                            <ul>
                                <li>- Experience as an integral part for at least two mobile app written in two different Android programming languages (Java, Android SDK, Play Store, etc.)</li>
                                <li>- Comfortable with extensive object oriented coding</li>
                                <li>- Great in documenting and commenting code written</li>
                                <li>- Experience with version control solutions and test-driven development</li>
                                <li>- Experience with multi-tenant development environments</li>
                                <li>- Fluent in English, preferably at least basic understanding of German</li>

                            </ul>


                            <a href="mailto:career@esportsconstruct.com" class="pull-right">APPLY NOW</a>

                        </div>
                        <!-- car slider item -->
                        <div class="car-slider-item">

                            <h2>Front End Developer (Intern / working student)</h2>

                            <p>We are looking for a talented developer at the start of his career who wants to join our team as a front end developer and help us build great user interfaces and experiences.</p>
                            <p>Please combine all application collaterals (CV, motivational letter, code excerpts) into a single PDF file.</p>
                            <p>Responsibilities:</p>
                            <ul>
                                <li>- Help develop the front end part of our own and client’s web solutions using HTML5, CSS3 and JavaScript</li>
                                <li>- Work in a collaborative environment with a variety of context and minimal guidance.</li>
                            </ul>
                            <p>Qualifications:</p>
                            <ul>
                                <li>- Prediploma in IT or related fields, alternatively a freshly completed apprenticeship as IT specialist in application development </li>
                                <li>- Sound working knowledge of HTML5 and CSS3</li>
                                <li>- Working knowledge of JavaScript fundamental concepts (closures, scoping, prototype, promises, etc)</li>
                                <li>- Understanding of frontend MVC/MVVM architectures</li>
                                <li>- Experience with Git and Git-flow</li>
                                <li>- Fluent in English, preferably at least basic understanding of German</li>
                                <li>- Motivation to learn quickly and apply learnt skills to practical projects in a commercial environment</li>
                            </ul>

                            <a href="mailto:career@esportsconstruct.com" class="pull-right">APPLY NOW</a>

                        </div>
                        <!-- car slider item -->

                        <div class="clearfix"></div>
                    </div>
                    <!-- car slider -->
                </div>
                <!-- car slider cont -->
            </div>
        </div>
        <!-- cont -->
    </div>
    <!-- our services -->
    <footer>
        <div class="cont">
            <div id="get-in-touch" class="pull-left">
                <div class="info-box">
                    <h2>Get in touch with us!</h2>
                    <p>Contact us by the number or e-mail adress below or simply write us an instant message with the contact form.
                    </p>
                </div>
                <p>Address: <br /> EsportsConstruct GmbH <br /> Weinbietweg 2 <br /> 67473 Lindenberg </p>
                <p>Phone: <a href="tel:+496325988490">+49 6325 988490</a></p>
                <p>e-mail: <a href="mailto:hello@esportsconstruct.com">hello@esportsconstruct.com</a></p>
            </div>
            <!-- get in touc -->
            <form class="pull-left" action="/contact" type="post">
                <div class="pull-left">
                    <input type="text" id="contactName" placeholder="Name" required>
                    <input type="email" id="contactMail" placeholder="Email" required>
                    <input type="text" id="contactPhone" placeholder="Phone">
                </div>
                <textarea class="pull-left" id="contactText" placeholder="Message" required></textarea>
                <div class="captcha-div">
                    <div class="g-recaptcha" data-sitekey="6Ldy3SITAAAAAFbPCW0hLgORgwFsM8qRSVwAaueP"></div>
                    <button type="submit" id="contactForm">send message</button>
                </div>
                <!-- captcha -->
            </form>
            <!-- cont -->
        </div>
        <!-- cont -->
        <div class="clearfix"></div>
    </footer>
    <div id="copyright">
        <div class="cont">

        </div>
    </div>
    <!-- copyright -->
    <div class="service-modals">
        <div class="modal fade" id="data-solutions" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Data Solutions</h4>
                    </div>
                    <div class="modal-body">

                        <p>EsportsConstruct data solutions help you understand the Esports landscape both on a macro and micro level with the help of highly customizeable information tools, all also available as live APIs. </p>
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="active"><a href="#esports-statistics" role="tab" data-toggle="tab" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services Data Solutions', 'eventAction': 'Tab Clicked', 'eventLabel': 'Statistics' });">Statistics</a></li>
                            <li><a href="#video-streaming" role="tab" data-toggle="tab" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services Data Solutions', 'eventAction': 'Tab Clicked', 'eventLabel': 'Video Streaming' });">Video Streaming</a></li>
                            <li><a href="#scouting-data" role="tab" data-toggle="tab" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services Data Solutions', 'eventAction': 'Tab Clicked', 'eventLabel': 'Scouting Data' });">Scouting Data</a></li>

                        </ul>

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="esports-statistics">
                                <div class="text-center">
                                    <img src="img/mountain-icon.png" alt="mountain">
                                </div>
                                <h2>Esports Statistics</h2>
                                <p>EsportsConstruct leverages a range of proprietary technology to collect all sorts of industry intel and constantly adds new data points to improve its offerings.</p>
                                <ul>
                                    <li> Complete list of all distinct Esports individuals and organisations
                                        <ul>
                                            <li> Track player performance with a wide range of indicators</li>
                                            <li> Monitor player transfers and measure organisation success</li>
                                            <li> Complete historic information backlog for all major Esports disciplines</li>
                                        </ul>
                                    </li>

                                    <li> Complete list of all relevant Esports matches, tournaments and events
                                        <ul>
                                            <li> Proprietary algorithm to evaluate the importance of all events, tournaments and matches</li>
                                            <li> Complete historic information backlog for all major Esports disciplines</li>
                                            <li> Real-time updates of forward-facing information
                                                <ul>
                                                    <li> All schedules and dates updated 24/7/365</li>
                                                    <li> Notifications for schedule and date changes</li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>


                                </ul>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="video-streaming">
                                <div class="text-center">
                                    <img src="img/play-cloud.png" alt="play">
                                </div>
                                <h2>Esports Video Streaming Data</h2>
                                <p>EsportsConstruct integrates with numerous broadcasting platforms to connect its database to the various live broadcast offers for all related entities.</p>
                                <ul>
                                    <li> Comprehensive overview of broadcasting platforms and channels covering each match</li>
                                    <li> Multi-language VOD content tracking</li>
                                    <li> Embed-ready API output</li>
                                </ul>

                            </div>
                            <div role="tabpanel" class="tab-pane" id="scouting-data">
                                <div class="text-center">
                                    <img src="img/person-icon.png" alt="person">
                                </div>
                                <h2>Esports Live Scouting Data</h2>
                                <p>EsportsConstruct utilizes its proprietary technology to update its database in real time and puts a team of seasoned industry veterans on top as an extra layer of verification</p>
                                <ul>
                                    <li> All critical match data updates in real-time</li>
                                    <li> Customizable feed of live in-game detail information (not available for all games)</li>
                                </ul>
                            </div>

                        </div>
                        <!-- tab content -->

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="ec-def-btn pull-left" data-dismiss="modal" data-toggle="modal" data-target="#esports-consultancy" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'Esports Consultancy' });"><span>Previous</span></button>
                        <button type="button" class="ec-def-btn" data-toggle="modal" data-target="#content-technology" data-dismiss="modal" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'Content Technology' });"><span>Next</span></button>
                    </div>
                </div>
                <!-- modal-content -->
            </div>
            <!-- modal-dialog -->
        </div>
        <!-- modal -->
        <div class="modal fade" id="esports-consultancy" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Esports Consultancy</h4>
                    </div>
                    <div class="modal-body">
                        <p class="text-center">
                            <img src="img/esports-consultancy-icon.png" alt="person">
                        </p>
                        <p>EsportsConstruct offers strategic advice through it’s sister company of Schmedeshagen Consulting who leverage years of experience in building successful services for Esports audiences. Our expertise focuses on:</p>
                        <p><i>Our expertise focuses on:</i></p>
                        <ul>
                            <li>M&A assistance including due diligence consulting</li>
                            <li>Market position risk and opportunity analysis, growth projections</li>
                            <li>Evaluation of existing products including suggestions with focus on value creation and investor ROI</li>
                            <li>Project management, analysis and optimization of processes and policies</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="ec-def-btn pull-left" data-dismiss="modal" data-toggle="modal" data-target="#marketing-solutions" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'Marketing Solutions' });"><span>Previous</span></button>
                        <button type="button" class="ec-def-btn" data-dismiss="modal" data-toggle="modal" data-target="#data-solutions" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'Data Solutions' });"><span>Next</span></button>
                    </div>
                </div>
                <!-- modal-content -->
            </div>
            <!-- modal-dialog -->
        </div>
        <!-- modal -->
        <div class="modal fade" id="user-interface" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Design</h4>
                    </div>
                    <div class="modal-body">
                        <p class="text-center">
                            <img src="img/ui-icon.png" alt="person">
                        </p>
                        <p>Having built industry-leading interfaces for both enterprise and consumer-centric services before, the team of EsportsConstruct utilizes the lessons learnt to create usability-focused and appealing visuals for:</p>
                        <ul>
                            <li>Consumer-facing website services</li>
                            <li>Consumer-facing mobile applications</li>
                            <li>Enterprise level website services</li>
                            <li>General software solutions</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="ec-def-btn pull-left" data-dismiss="modal" data-toggle="modal" data-target="#content-technology" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'Content Technology' });"><span>Previous</span></button>
                        <button type="button" class="ec-def-btn" data-dismiss="modal" data-toggle="modal" data-target="#front-end" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'Front End Technology' });"><span>Next</span></button>
                    </div>
                </div>
                <!-- modal-content -->
            </div>
            <!-- modal-dialog -->
        </div>
        <!-- modal -->
        <div class="modal fade" id="marketing-solutions" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Marketing Solutions</h4>
                    </div>
                    <div class="modal-body">
                        <p>EsportsConstruct leverages its teams vast background in Esports content creation to help clients provide plug-and-play solutions to their audiences by not just building the services needed, but also by filling these frames with content created by experts in their respective realms.</p>

                        <ul class="nav nav-tabs" role="tablist">
                            <li class="active"><a href="#content-tab" role="tab" data-toggle="tab" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services Marketing Solutions', 'eventAction': 'Tab Clicked', 'eventLabel': 'Editorial Content' });">Editorial Content</a></li>
                            <li><a href="#social-media-tab" role="tab" data-toggle="tab" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services Marketing Solutions', 'eventAction': 'Tab Clicked', 'eventLabel': 'Socia lMedia' });">Social Media</a></li>
                            <li><a href="#translations-tab" role="tab" data-toggle="tab" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services Marketing Solutions', 'eventAction': 'Tab Clicked', 'eventLabel': 'Scouting Data' });">Scouting Data</a></li>

                        </ul>

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="content-tab">
                                <p class="text-center">
                                    <img src="img/content.png" alt="content">
                                </p>
                                <h2>Editorial Content</h2>

                                <ul>
                                    <li>
                                        In-house team of content creators
                                        <ul>
                                            <li>Video content</li>
                                            <li>Editorial content</li>
                                        </ul>
                                    </li>
                                    <li>Huge network of freelance experts for all major Esports disciplines</li>
                                </ul>
                            </div>
                            <!-- tab pane -->
                            <div role="tabpanel" class="tab-pane" id="social-media-tab">
                                <p class="text-center">
                                    <img src="img/social-media.png" alt="person">
                                </p>
                                <h2>Social Media</h2>
                                <p><i>Social Media Details:</i></p>
                                <ul>
                                    <li>Strategy development</li>
                                    <li>Content planning and scheduling</li>
                                    <li>Orchestrated publishing across all channels</li>
                                </ul>
                            </div>
                            <!-- tab pane -->
                            <div role="tabpanel" class="tab-pane" id="translations-tab">
                                <p class="text-center">
                                    <img src="img/scouting-data.png" alt="person">
                                </p>
                                <h2>Translations</h2>
                                <ul>
                                    <li>Available languages among others: EN, CN, DE, RU</li>
                                    <li>More languages available through our partner network upon request</li>
                                </ul>

                            </div>
                            <!-- tab pane -->

                        </div>
                        <!-- tab content -->

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="ec-def-btn pull-left" data-dismiss="modal" data-toggle="modal" data-target="#back-end" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'Back End Technology' });"><span>Previous</span></button>
                        <button type="button" class="ec-def-btn" data-dismiss="modal" data-toggle="modal" data-target="#esports-consultancy" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'Esports Consultancy' });"><span>Next</span></button>
                    </div>
                </div>
                <!-- modal-content -->
            </div>
            <!-- modal-dialog -->
        </div>
        <!-- modal -->
        <div class="modal fade" id="front-end" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Front-End Technology</h4>
                    </div>
                    <div class="modal-body">
                        <p class="text-center">
                            <img src="img/frontend-tech-icon.png" alt="person">
                        </p>
                        <p>EsportsConstruct offers execution of ideas and designs leveraging its team of software engineers to support clients with turn-key solutions.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="ec-def-btn pull-left" data-dismiss="modal" data-toggle="modal" data-target="#user-interface" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'User Interface And Experience' });"><span>Previous</span></button>
                        <button type="button" class="ec-def-btn" data-dismiss="modal" data-toggle="modal" data-target="#back-end" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'Back End Technology' });"><span>Next</span></button>
                    </div>
                </div>
                <!-- modal-content -->
            </div>
            <!-- modal-dialog -->
        </div>
        <!-- modal -->
        <div class="modal fade" id="content-technology" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Content-Technology</h4>
                    </div>
                    <div class="modal-body">
                        <p class="text-center">
                            <img src="img/content-tech-icon.png" alt="person">
                        </p>
                        <p>Content at heart, EsportsConstruct builds workflow-optimized content management systems and provides clients with their proprietary technology via versatile interfaces and feeds to supply their own products and services at minimum maintenance overhead.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="ec-def-btn pull-left" data-dismiss="modal" data-toggle="modal" data-target="#data-solutions" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'Data Solutions' });"><span>Previous</span></button>
                        <button type="button" class="ec-def-btn" data-dismiss="modal" data-toggle="modal" data-target="#user-interface" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'User Interface And Experience' });"><span>Next</span></button>
                    </div>
                </div>
                <!-- modal-content -->
            </div>
            <!-- modal-dialog -->
        </div>
        <!-- modal -->
        <div class="modal fade" id="back-end" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Back-End Technology</h4>
                    </div>
                    <div class="modal-body">
                        <p class="text-center">
                            <img src="img/backend-tech-icon.png" alt="person">
                        </p>
                        <p>From cloud applications to full-stack server and cluster management, EsportsConstruct manages all aspects of technology in-house and offers clients to take advantage of such managed solutions.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="ec-def-btn pull-left" data-dismiss="modal" data-toggle="modal" data-target="#front-end" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'Front End Technology' });"><span>Previous</span></button>
                        <button type="button" class="ec-def-btn" data-dismiss="modal" data-toggle="modal" data-target="#marketing-solutions" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Services', 'eventAction': 'Service Dialog Opened', 'eventLabel': 'Marketing Solutions' });"><span>Next</span></button>
                    </div>
                </div>
                <!-- modal-content -->
            </div>
            <!-- modal-dialog -->
        </div>
        <!-- modal -->

    </div>
    <!-- service modals -->
    <div class="modal fade" id="imprint" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-right">
                    <button class="ec-def-btn" data-dismiss="modal" aria-label="Close"><span>Close</span></button>
                </div>
                <div class="modal-body">
                    <p>Firma: EsportsConstruct GmbH</p>
                    <p>Vertreter: Mathias Schmedeshagen</p>
                    <p>Anschrift: Weinbietweg 2 - 67473 Lindenberg</p>
                    <p>Tel: +49 (0) 6325 988-490</p>
                    <p>Internet: www.esportsconstruct.com</p>
                    <p>E-Mail:hello[at]esportsconstruct.com</p>
                    <p>Sitz: 67473 Lindenberg</p>
                    <p>USt-IdNr: DE306760362</p>
                    <p>Steuernummer: 31/659/03122</p>
                    <p>Registergericht: Ludwigshafen</p>
                    <p>Registernummer: HRB 64961</p>
                    <p>Inhaltliche Verantwortung gem. § 6 MDStV: Mathias Schmedeshagen</p>
                    <h3>HAFTUNGSHINWEIS</h3>
                    <p>Trotz sorgfältiger inhaltlicher Kontrolle übernehme ich keine Haftung für die Inhalte externer Links. Für den Inhalt der verlinkten Seiten sind ausschließlich deren Betreiber verantwortlich. Inhalt und Struktur der www.esportsconstruct.com Webseite ist urheberrechtlich geschützt. Die Vervielfältigung von Informationen oder Daten, insbesondere die Verwendung von Texten, Textteilen oder Bildmaterial, bedarf der vorherigen Zustimmung der EsportsConstruct GmbH </p>
                    <h3>COPYRIGHT</h3>
                    <p>Alle Inhalte, das Design der Oberfläche und der Quelltext sowie alle Texte sind urheberrechtlich geschützt. Jegliche Nutzung ist nur mit schriftlicher Genehmigung durch die EsportsConstruct GmbH oder der jeweiligen Rechteinhaber gestattet. In jedem Fall gelten die gesetzlichen Copyright-Bestimmungen. Bei Verstoß werden umgehend strafrechtliche Schritte eingeleitet.</p>
                    <h3>DATENSCHUTZ</h3>
                    <p>Wir, die EsportsConstruct GmbH – nehmen den Schutz Ihrer persönlichen Daten sehr ernst und halten uns strikt an die Regeln der Datenschutzgesetze. Personenbezogene Daten werden auf dieser Webseite nur im technisch notwendigen Umfang erhoben. In keinem Fall werden die erhobenen Daten verkauft oder aus anderen Gründen an Dritte weitergegeben.</p>
                    <p>Die nachfolgende Erklärung gibt Ihnen einen Überblick darüber, wie wir diesen Schutz gewährleisten und welche Art von Daten zu welchem Zweck erhoben werden.</p>
                    <p>Datenverarbeitung auf dieser Internetseite</p>
                    <p>Die EsportsConstruct GmbH erhebt und speichert automatisch in seinem Server Log File Informationen, die Ihr Browser an uns übermittelt. Dies sind:</p>
                    <ul>
                        <li>Browsertyp/-version</li>
                        <li>verwendetes Betriebssystem</li>
                        <li>Referrer URL (die zuvor besuchte Seite</li>
                        <li>Hostname des zugreifenden Rechners (IP Adresse)</li>
                        <li>Uhrzeit der Serveranfrage</li>
                    </ul>
                    <p>Diese Daten sind für die EsportsConstruct GmbH nicht bestimmten Personen zuordenbar. Eine Zusammenführung dieser Daten mit anderen Datenquellen wird nicht vorgenommen, die Daten werden zudem nach einer statistischen Auswertung gelöscht.</p>
                    <p>Übermittlung von Daten durch Formulare</p>
                    <p>In verschiedenen Bereichen stehen Ihnen auf unserer Internetseite Formulare für die Kontaktaufnahme zur Verfügung. Die von Ihnen übermittelten Daten werden nur für diesen Zweck verwendet. Diese Daten werden nicht auf dem Web-Server gespeichert, sondern ausschließlich per automatisierter E-Mail an den dafür vorgesehenen Ansprechpartner in unserem Hause weitergeleitet. Die Weitergabe der in dieser E-Mail enthaltenen Daten an Dritte erfolgt nicht. Wir kümmern uns um die Einhaltung der Datenschutzbestimmungen in allen Bereichen. Wenn Sie Fragen haben, können Sie sich auch direkt an uns wenden.</p>
                    <p>Auskunftsrecht</p>
                    <p>Sie haben jederzeit das Recht auf Auskunft über die bezüglich Ihrer Person gespeicherten Daten, deren Herkunft und Empfänger sowie den Zweck der Speicherung. Auskunft über die gespeicherten Daten gibt der Datenschutzbeauftragte Mathias Schmedeshagen – hello[at]esportsconstruct.com.</p>
                    <p>Schutzrechtsverletzung</p>
                    <p>Falls Sie vermuten, dass von dieser Website aus eines Ihrer Schutzrechte verletzt wird, teilen Sie uns dies bitte umgehend mit, damit entsprechende Abhilfe geschaffen werden kann: hello[at]esportsconstruct.com.</p>
                </div>
            </div>
            <!-- modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>
    <!-- imprint modal -->
    <div class="modal fade log-res-modals" id="login-modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">LOGIN</h4>
                </div>
                <div class="modal-body">
                    <form action="">
                        <input type="text" placeholder="Username" required>
                        <input type="password" placeholder="Password" required>
                        <button type="submit" class="ec-def-btn"><span>SIGN IN</span>    </button> <a data-toggle="modal" data-target="#reset-pass-modal" data-dismiss="modal">Lost Your Password ?</a>
                    </form>

                </div>
            </div>
            <!-- modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>
    <!-- modal -->
    <div class="modal fade log-res-modals" id="reset-pass-modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Reset password</h4>
                </div>
                <div class="modal-body">
                    <form action="">
                        <input type="email" placeholder="E-mail" required>
                        <button type="submit" class="ec-def-btn"><span>REQUEST</span>    </button>
                    </form>

                </div>
            </div>
            <!-- modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>
    <!-- modal -->
    <div class="modal fade " id="msg-succes" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Message sent!</h4>
                </div>
                <div class="modal-body text-center">
                    <h3>Your message has been successfully sent.</h3>
                    <p>We will send you a reply as soon as possible.</p>
                    <button class="ec-def-btn" data-dismiss="modal"><span>Ok</span></button>
                </div>
            </div>
            <!-- modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>
    <!--sucess modal -->
    <div class="modal fade " id="msg-fail" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Message not sent!</h4>
                </div>
                <div class="modal-body text-center">
                    <h3>Your message has not been sent.</h3>
                    <p>We apologize, please try later.</p>
                    <button class="ec-def-btn" data-dismiss="modal"><span>Ok</span></button>
                </div>
            </div>
            <!-- modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>
    <!--fail modal -->

    <script src="/js/jquery-2.1.1.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {

            // clicking navigation calling slide function
            $('#services-link').click(function() {
                slideTo($('.our-services'));
            });
            $('#career-link').click(function() {
                slideTo($('.careers'));
            });
            $('#contact-link, #land-page .jumbo button').click(function() {
                $('html, body').animate({
                    scrollTop: $(document).height()
                });
            });
            $('#home-link').click(function() {
                $('html, body').animate({
                    scrollTop: '0px'
                });
            });

            // when scrolling showing active state to navigation
            $(document).scroll(function() {
                var $scrollPos = $('html, body').scrollTop() || $('body').scrollTop();
                var $servicesPos = $('.our-services').offset().top;
                var $careerPos = $('.careers').offset().top;
                var $contactPos = $('footer').offset().top;

                if ($scrollPos < $servicesPos) {
                    $('.navbar-nav li').removeClass('active');
                }
                if ($scrollPos >= $servicesPos) {
                    $('.navbar-nav li').removeClass('active');
                    $('#services-link').parent().addClass('active');
                }
                if ($scrollPos >= $careerPos) {
                    $('.navbar-nav li').removeClass('active');
                    $('#career-link').parent().addClass('active');
                }

                if ($(window).scrollTop() + $(window).height() == getDocHeight()) {
                    $('.navbar-nav li').removeClass('active');
                    $('#contact-link').parent().addClass('active');
                }

            });
            // getting document height
            function getDocHeight() {
                var D = document;
                return Math.max(
                    D.body.scrollHeight, D.documentElement.scrollHeight,
                    D.body.offsetHeight, D.documentElement.offsetHeight,
                    D.body.clientHeight, D.documentElement.clientHeight
                );
            }
            // sliding to coresponding section
            function slideTo(elem) {
                var $position = elem.offset().top + 1;
                $('html, body').animate({
                    scrollTop: $position + 'px'
                });
            }
            //slider left arrow
            $('#slider-arr-left').click(function() {
                $numb_slides = 2;
                if (window.matchMedia('(max-width: 1550px)').matches) {
                    $numb_slides = 1;
                }
                $width_elem = $('#land-page .car-slider-item').outerWidth() + 80;
                $elem_elem = ($('#land-page .car-slider-item').length - $numb_slides) * ($width_elem);
                $margin = parseInt($('.car-slider').css('margin-left'));
                if ((-$elem_elem) < $margin) {
                    if (!$('.car-slider').is(':animated')) {
                        $('.car-slider').stop(true, true).animate({
                            marginLeft: '-=' + $width_elem + 'px'
                        });
                    }
                } else {
                    if (!$('.car-slider').is(':animated')) {
                        $('.car-slider').stop(true, true).animate({
                            marginLeft: '0px'
                        });
                    }
                }
            });
            //slider right arrow
            $('#slider-arr-right').click(function() {

                $numb_slides = 2;
                if (window.matchMedia('(max-width: 1550px)').matches) {
                    $numb_slides = 1;
                }
                $width_elem = $('#land-page .car-slider-item').outerWidth() + 80;
                $margin = parseInt($('.car-slider').css('margin-left'));
                if ($margin < 0) {
                    if (!$('.car-slider').is(':animated')) {
                        $('.car-slider').stop(true, true).animate({
                            marginLeft: '+=' + $width_elem + 'px'
                        });
                    }
                } else {
                    if (!$('.car-slider').is(':animated')) {
                        $elem_elem = ($('#land-page .car-slider-item').length - $numb_slides) * $width_elem;
                        $('.car-slider').stop(true, true).animate({
                            marginLeft: -$elem_elem + 'px'
                        });
                    }
                }

            });
            // reseting slider when resizing
            $(window).resize(function() {
                $('.car-slider').stop().animate({
                    marginLeft: '0px'
                });
            });

            $("#contactForm").on('click', function(e) {
                e.preventDefault();

                sendContact();
            });

            var RC2KEY = '6Ldy3SITAAAAAFbPCW0hLgORgwFsM8qRSVwAaueP';

            function reCaptchaVerify(response) {
                if (response === document.querySelector('.g-recaptcha-response').value) {
                    sendContact();
                }
            }

            function reCaptchaExpired() {
                /* do something when it expires */
            }

            function reCaptchaCallback() {
                grecaptcha.render('id', {
                    'sitekey': RC2KEY,
                    'callback': reCaptchaVerify,
                    'expired-callback': reCaptchaExpired
                });
            }

            function sendContact() {
                $.ajax({
                    type: 'POST',
                    url: "/contact",
                    data: {
                        name: $('#contactName').val(),
                        mail: $('#contactMail').val(),
                        phone: $('#contactPhone').val(),
                        text: $('#contactText').val(),
                        recaptchaResponse: $('.g-recaptcha-response').val()
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            $('#msg-succes').modal('show');

                            $('#contactName').val('');
                            $('#contactMail').val('');
                            $('#contactPhone').val('');
                            $('#contactText').val('');
                        } else {
                            $('#msg-fail').modal('show');
                        }
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }
        });

    </script>
</body>

</html>
