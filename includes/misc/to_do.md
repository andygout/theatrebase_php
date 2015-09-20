Next Steps
-------

#### Misc.

- Check data entered correctly for all fields (w/ Command Prompt) (esp. data w/ and w/out suffix).

- Use BIGINT for all fields’ primary keys (i.e. prd_id) to future-proof?

- SEO (meta-data) – ensure pertinent data is entered for each page.

- Are thtr_fll_nm / prsn_fll_nm / plytxt_nm_yr reqd or just comprise from elements?

- Only allow display/edit for ‘^the-company$’, ‘tbc$’ during logged in session.

- URL and xx_alph w/non-Roman (Chinese / Japanese / Cyrillic) characters. Tena Štivičić -> generateurl = tena-tivi-I (must accommodate intl chars) ([Link #1](http://stackoverflow.com/questions/10054818/convert-accented-characters-to-their-plain-ascii-equivalents); [Link #2](http://stackoverflow.com/questions/3542717/how-to-remove-accents-and-turn-letters-into-plain-ascii-characters); [Link #3](http://stackoverflow.com/questions/3371697/replacing-accented-characters-php).

- Person/company/material: single credits table combining all fields, i.e. writer, performer, creative credits consolidated in one table (a la IMDb).

- All credit tables: sort into past/current/future (a la WhatsOnStage).

- Suffixes: roman -> numeric (and increase person suffix above 99).

- Shared include files: repeated heads from display files (+ elsewhere?).

- Amend validation where (i.e.) person entered (production, playtext, company, etc.) so syntax to avoid in prsn-edit is also avoided here. i.e. awards: ppl##prds++plytxts -> plytxt !include [##]; rel_tm/lctn/plc !include [,,], [##], [++] / prsn/prf_rl/us_rl !include ‘[alt]’.

- Validation (all) – can $xx_err_array be removed (other than those set in specific places)?

- Amend instances of giving suffix but no subject (with no checks thereafter); results in ‘--2’ as subject.

- Production created/updated data necessary?

- Handling for playtext-publisher (issues of numerous publishers for older playtexts…)?

- Home page: productions opening next week/now playing: order so first and press dates both used to order (given some productions won’t have press dates) + display press/opening date for productions opening in nxt week.

- All empty values=NULL where required (rather than ‘’ or ‘0’).

- Production: link to previous/next production (by opening date) (a la IBDB).

- External links, i.e. official site / Wikipedia / IMDb / Amazon / Spotlight / YouTube / articles.

- Person: John Smith (given-family); Wang Gongxin (family-given) – choose order for prsn_fll_nm.

- Person alias: Andy Smith -> a smith / Phoebe Waller-Bridge -> Phoebe Whyte / Clare ‘Spanna’ Hill

- Location alias: The Big Apple -> NYC / Myanmar -> Burma / Saigon -> Ho Chi Minh City / The Imperial Palace -> The Great Palace of Constantinople

- Person prefixes/suffixes: Rev Bazil Meade / Sir Howard Panter / Julia Horan CDG / Justin Huff CSA

- Person: Madonna / Ultz / Dr Seuss; the latter requires ability for first or last name (or both); not just first.

- Person – production/playtext role note, i.e. `Vicky;;Featherstone::AD;;inaugural production`? (a la performer & understudy handling)

- Performer/understudy: CURIOUS INCIDENT (TR 2014, Issue 14): Abram Rooney \[alt\] (some performances).

- Plytxt: EPIGONI by Sophocles written in 5th Century (specific date is unknown).

- Playtext: Only one ‘circa’ date (not one each for yr_strtd and yr_wrttn), or ensure unused are NULL.

- Playtext/year: If playtext yr_strtd exists, display playtext if displayed year falls between yr_strtd and yr_wrttn.

- Should season and festival be assigned year(s) as part of their name?

- If production is collection overview, inherit and display all collection segment credits (i.e. all accredited comapnies and people).

- Playtext display: List songs (inc. credits) (for musicals) and monologues (for plays).

- Playtext series:- links to subsequent volumes (TIM CROUCH: PLAYS 1, 2, 3, etc.: playtext/series/URL).

- Material: years for materials (if applicable), i.e. Buster Keaton film (1920); Frank Wedekind plays.

- Related materials: link ‘Biblical text’ to ‘Religious text’? ‘Motion Picture Screenplay’ to ‘Screenplay’, etc?

- Related abilities: Ability -> Related ability?

- Playtext collection: display ‘Collection’ as link?

- Character age and description given when entering into playtext (rather than on character page)?

- Truncate error_for/errorfield on edit pages (remove <?php ?> repeats).

- Producers – use of ‘;’ is incorrect: `Produced by::Headlong Theatre||Artistic Director~~Jeremy;;Herrin>>Duncan C;;Weldon>>and~~Spymonkey||>>in association with~~Finborough Theatre||Artistic Director~~Neil;;McPherson*`

- Home page: If tour previewing/opening next week, show applicable tour leg(s).

- Production: If collection segment, display collection overview when displaying tour legs.

- Theatre-subtheatre: Can sur-theatre be called via join (and not stored as a value in theatre table)?

- Season/festival – allow multiple seasons/festivals for each production: MARIA ADDOLORATA part of two festivals (2015/p.132)?

- Production – producers (people) of two different companies co-producing the show (2015/p.146) – will cause duplicate error.

- 'The JP Morgan Award for Emerging Directors Production' (2015/5/p.203) - how to display on production page?


#### Optional sub-headers

- Production performers/understdudies – character groups (per playtext) and KINDNESS OF STRANGERS: ‘Video and Pre-Recordings’ (2014/14).

- Prodution: creative – i.e. ADLER & GIBB: main creatives listed without header, then ‘US Film Team’ listed under header.


#### Performers (inc. understudies)

- Companies as performer/understudy: Idiots Of Ants / Gorgeous Bourgeois & Maurice Maurice / Frisky & Mannish / All Or Nothing Aerial Perfs (2014/15/p.800) / THE MALCONTENT @ Shakespeare's Globe (Globe Young Players) / HORROR SOUK (2014/22) / LA SOIREE (2014/23)

- [alt]: display who role is shared with (w/ multiple casts check for overlap of dates?).

- Subsequent casts: split casts (opening/subsequent/current) in same way as categories for awards ([@@]). If casts given > 1 then dates must be assigned to performers & understudies (and ensure links to character page correctly).


#### Company-member

- Allow person to have multiple roles (with respective dates) in same company (i.e. promoted over years or hold two different roles, i.e. SJD, ‘Managing Director / Agent: Literary’).

- Categorise lists by department (with optional sub-header)? Useful for previous company members (v. long lists), i.e. `Artistic Team||Nicholas;;Hytner::AD//Howard;;Davies::ED,,Administrative Team||…`, etc.

- Past members (and accordingly should appear on person's credits as ‘past positions’ held).

- Assign associate companies (and people), i.e. Bush Theatre ->Iron Shoes (error: can’t assign company as associate of self).

- Agencies can represent companies (as well as people).


#### Characters

- ‘Lords’ -> ‘Lord’ (individual performers will perform role as singular) results in variant name; only display variant names if char_amnt==1? MACBETH: Three Witches aka Three Weird Sisters.

- Char_suffix goes up to 999,999, which creates roman suffix CMXCMXCMXCIX; integers instead of roman numerals? (and apply to all other suffixes for consistency?). Better for jQuery results?

- Char_suffix_num: MEDIUMINT, INT or BIGINT? (Easy to transfer data to new column type later?).

- Awards won for performances of this character?

- Cast count when different minimum and maximum cast sizes (or optional doubling/tripling) are given, i.e. ROCK ‘N’ ROLL: *Esme in Act One and Alice are to be played by the same actress; similarly Eleanor and Esme in Act Two. Further doubling (or tripling) (of roles) is optional. The intention is that the 20 characters may be played by a company of 11 or 12*. Maybe set of boxes each for male/female/non-specific/additional; one generally left empty, others used when range needs to be created, else ignored. Char_age could employ this system; currently if exact age given then same age must be entered in both boxes, but how will this affect searches, i.e. character whose age is between 25-30; which fields are searched?).

- AN AUGUST BANK HOLIDAY LARK: John Farrar (50+) – range? CIPHERS: ‘Late fifties/early sixties’; give option to display text (as link?) over numbers (given exact range is disputable); use space vacated by ‘group’.

- 100 GREAT PLAYS FOR WOMEN: Cast breakdown: 8f, 6m (doubling possible)


#### Awards

- Production awards: display (at nomination/win count) – ‘Including from previous/subsequent runs of production (when applicable).

- The Stage Awards: Theatre of the Year – use comps as nominee/winner or create handling for theatre?

- Awards name changed: TMA Theatre Awards -> UK Theatre Awards

- Hosts (overall and for individual categories) and team (see Olivier Awards 2013 programme): creatives/producers/musicians/production team. Critics’ Circle Awards programme – problem: Ian Shuttleworth assigned to two companies (choose one?).

- Type of award: i.e. playwriting / acting, etc.

- URL: awards/ceremony/laurence-olivier-awards/2010 -> awards/laurence-olivier-awards/2010 – poss?

- Award credits on theatre display page (i.e. as venue): UNION with production credits possible?


#### Display

- TheatreBase header – lighter blue around edges of text and logo (also for favicon).

- Page cannot contain a (i.e. production/theatre/playtext) name of long, unbroken string; i.e. Supercalifragilisticexpialidocious (or longer) would not only overspill container but off screen (can potentially be up to 255 characters long) – how to remedy? Ditto awards/year error for: == > 1 (equal sign excess; as whole string (usually spaceless) is echoed).

- Force line break or fix table width for unbroken strings (i.e. ‘mmm…’ x20), else table will widen.

- Production tables: allow left column (people) to use more width if other column (roles) not using it (and vice versa).

- Column titles on table headers (i.e. Production: production name/theatre/overall dates).

- Background colour for prod1 table rows (production display credits).

- Decide on table header wording for Material (Harper Regan (play)) (i.e. Material/Other productions of xxx).

- Character display: if multiple actors have performed role (production table) then align credits, i.e.:-
  ```
  Ian McKellen … Sorin (currently askew)
  William Gaunt … Sorin
  ```

- CSS – text change to Calibri (why does size of text not change consistently throughout site?).

- Different background image depending on field being displayed (give container slight transparent effect? Tint background blue to fit with site’s colour scheme): i.e. Home (auditorium) / Production (stage) / Person (dressing room) / Playtext (pages of book) / Location (globe) / Time (clock) / Character (prop & costume store).

- Agent/Licensor: new display tables/decide on wording (‘writing’/‘sound composing’/‘set designing’).

- Horizontal scrolling: includes right-padding when reduced (should only display width of container).

- Centre (edit page) textarea: equal padding on right (without allowing widening/narrowing).

- Amend print CSS (bold/thick table corners) – possibly caused by curved corners dictation.

- Print: prevent page break mid-page (i.e. mid-table).

- Widen playtext display table column for txt_vrsn_nm (not wide enough for multiple txt_vrsn_nms).

- Awards: ‘Also awarded’ (i.e. in same category) – display such results in box to differentiate (or use different thickness of borders throughout).

- ‘Theatre’ – display as ‘Venue’(?).

- Production display – producers/musicians: if multiple companie listing members, align all tables (not individually).

- Setting: Alternate background colours of table; wrap header so wording ‘Setting’ sticks out top of table.

- Theatre/company – theatre/type, company/location, etc. – display data in far right column (i.e. opening/estimated dates?).

- Theatre: seating plan.

- Cookies notification (use current EU cookie policy)

- Disclaimer: ‘we endeavour for records to be accurate’, etc.

- Credit rows: centre align (not top align) so as to allow for images.

- Decide on person: clients display table (widen far-right column if bordered option) and change that used for playtext: licensors display table (for visual continuity).

- Check display in other browsers (i.e. production table, far-right dates column displayed in Internet Explorer).


#### Javascript / jQuery

- Autotab (for date entry: automatically tabs to next field once maxlength has been reached) / Page tabs / Datepicker / Autocomplete (tour overview – identifies individual legs) / Form – live validation / Form – dependent fields (tour overview)

- Pagetabs (for all fields with multiple credit tables where necessary): plus tabs within tabs, i.e. person: Production [wri/producer/perf, etc.]; Playtext [wri/source_mat_wri]; Employee [companies].

- Character display: Toggle between full list of roles (if multiple roles performed) and specified role only (i.e. Oberon: John Smith … Theseus / Oberon / Lysander –OR– John Smith … Oberon).

- Person: Toggle between person and understudy credits in two separate tables and in a single combined table.

- Character display: Only display exact production matches (don’t use character link); ‘Irina’ will not match ‘Arkadina’.

- Character credits: Toggle with productions associated only via playtext (i.e. not other versions of text/related via source material).

- Production/playtext company: person/person/person – option to toggle between different ordering (each company and its members one at a time; all companies then all members).

- Playtext display page: List character display table by: name/sex/age/group/char_amount, etc.

- Ability to order production tables by first preview/press/last performance (but how to display press performance, esp. if NULL?).

- Company members: filter by role, i.e. `%Artistic Director%` (will only list people who have performed role).

- Production credits: also filter by role in same way (why role note should be kept separate from role itself).

- Production add/edit menu: tour/collection/rep – order by prd_frst_dt (rather than prd_id).

- Toggle to display var_char credits as part of/in separate lists.

- Playtext/year: toggle order between plytxt_alph / release date

- Awards (year; category; others?): display tables with all nominees/winners only.

- Awards (year/category): links at top: auto scroll to div below rather than page refresh.

- Award year list: Toggle between awrd_dt / awrd_nm

- Display page: Twitter feed of respective person or company.

- Rome (Italy, Europe) – hide bracketed info and only display during hover (time/place/location/genre).

- Time span (i.e. 1998 to 2013): click to toggle full range/first-last.

- Setting (time): timeline.

- Person: writer productions – toggle between list all together and separately (writer/original writer/source material writer).

- Production – collection also comprises: hover to display theatre & dates.

- Person-profession: filter by place of origin (i.e. all writers from Hackney). Also: comp_type filter by location.

- Previous/subsequent runs of production: expand tour overview entry to display all tour legs.

- Theatre/company: for those with previous/subsequent name, toggle to display credits under that name only, or all credits.

- Theatre / company / setting (location:production and playtext) / person + character origin: Google maps display (home page to include theatres map).

- Person: ethnicity / place of origin / profession – extra field to enter prsn_nm + filter results. Likewise for production: genre / setting / playtext: year / text version / setting / company / theatre, etc.


#### Search (header) function

- Search function: homophone matching (Katie Stevens = Katy Stephens / Chuck Iwugee = Chuk Iwuji).

- Pressing ‘search’ button causes page refresh of sorts (submitting prd_id). ‘Add’ page: causes error as no prd_id yet to be submitted. ‘Edit’ page: returns to original production display page for that entry.

- Search production by prd_nm and prd_alt_nm / company by comp_nm and comp_reg_nm.


#### Edge cases

- Production/playtext: for multiple (3+?) source materials by same writer(s) apply [;].

- Production/playtext: src_mat_sb_rl: i.e. Based on novel and inspired by poem by Writer(s) – only required if 2 x different source materials be same writer(s) with different relationship to production/playtext are used – v. unlikely; only apply if example arises.

- ‘members-of-the-company’: more wording that should be displayed without link.


#### Hosting

- What happens to controlling website if I get new computer?  Is access to FTP only thing required?

- Is separate web hosting required for each separate website/domain?