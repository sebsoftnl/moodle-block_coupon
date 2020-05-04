
SEBSOFT COUPON PLUGIN

The Sebsoft Coupon Plugin offers you the possibility to create coupons for various levels
of course access. Using a coupon code, users will be enrolled into courses that are attached to the coupon.

There are a few different ways of generating coupons:
- Course level: this means one or more course(s) need to be selected for which the coupon is valid.
  Once the coupon code is entered by a user, he or she will be enroled in every course the coupon
  is attached to.
  A nice extra feature is, when groups are configured for a certain course, you can configure which
  course group the coupon is for. When a user claims the coupon, he or she will be added to that specific
  group in the course. This makes it possible to separate coupon users from regular users.
  You could also make specific course groups every time you generate a batch of new coupons, so there's
  some way of matching a batch of coupons to a group of users. The possibilities are numerous.

- Cohort level: this means one or more cohort(s) need to be selected for which the coupon is valid.
  Once the coupon code is entered by a user, he or she will be added as a cohort member for every
  cohort the coupon is attached to.
  NOTE: When generating cohort level coupons, there is a possibility to "connect" extra courses to
  a given cohort. One MUST know that upon doing this, the courses will at that point be added to
  the cohort enrolment sync. Without knowing this little fact, it could be "unexpected" behavior,
  even though it's a logical step.

Furthermore, coupons can be generated on two "access" levels:
- Personalized. The base of this, is either a CSV is uploaded, or a CSV is given directly in a textbox.
  The needed user information for every coupon to be generated will be the user's name, email address
  and gender.
  These coupons will then be generated and sent off to the appropriate users using a background task.

- Bulk: The base of this is simple: you generate a certain amount of access coupons, which will then
  be emailed to the pre-configured recipient or (if allowed) to an emailaddress that can be freely entered.

- WARNING:
  Wherever possible, please use PNG images.
  Also, whenever possible, do NOT use images that have an alpha channel.
  This will MASSIVELY slow down processing time, due to internal conversion of images by TCPDF.
  On our tests, for a 300 DPI image on A4 format (2480 x 3508 pixels) with an alpha channel,
  rendering a single PDF took around 25 seconds. Taking the alpha channel out of the image,
  rendering a single PDF was reduced to around 3 seconds.
  This means the PNG images should _always_ be stored as 24-bit true colour images.
  _Do not use 32-bit_ (true colour + transparency)!
  Furthermore, whenever possible, have the coupon generator create a single PDF with all
  coupons (this is only applicable when generating coupons in "bulk" mode). The process of
  generating coupons has been optimized to only "use" the image 1 time when creating any amount
  of coupons in a single PDF. If you choose the option to create a seperate PDF for every coupon,
  the image will have to be rendered for each and every PDF.
  This has 2 major downfalls:
  1. Every PDF will roughly have a slightly bigger size than the image size (in our tests, 350 - 400 kB per PDF)
  2. Every PDF takes the full amount of time to render.
  Effectively, this means the following (using fictitious sizes):
  10 coupons, single PDF -> result is around 400 kB, rendering takes around 3-4 seconds.
  10 coupons, seperate PDFs -> result is around 4 MB, rendering takes around 35-40 seconds.

INSTALLATION INSTRUCTIONS

- Copy the coupon folder to your blocks directory.
- Go to the moodle admin pages (you will probably be confronted with it anyway) and install the plugin
- Configure the main settings for the plugin.
- We're set up for usage!

PLACEMENT OF THE PLUGIN

There are only two pages you can add the coupon block on. This is the site's frontpage
or the user dashboard (my) page. The main reason for this is because the block is the
"frontend" for users to enter their coupon or voucher code.
Administrators and users with the correct capabilities will also see the links to
the coupon administration pages and the coupon generator page.

GENERAL CONSIDERATIONS

There's two main configurations to consider when generating coupons.
For course type coupons, this plugin attempts to enrol a user through use of Moodle's
internal function "enrol_try_internal_enrol".
The Moodle documentation shows it will attempt to enrol you using manual enrolment.
For this reason manual enrolment MUST be enabled for these courses.

For cohort type coupons, a user is made a member of a cohort. That's all there is to it.
Surprise though, if you want to actually make a user have access to a course, you
probably want to configure cohort enrolment for the course.

For those paying attention, creating cohort type coupons thus enables a simple
way of enrolling users into multiple courses at once just by configuring the right
cohort enrolments in multiple courses.
That's still not to say course coupons can't be used in a similar way (using meta courses).
