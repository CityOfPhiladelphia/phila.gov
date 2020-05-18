<div class="row event-row medium-collapse equal-height" data-open="[id]">
  <div class="small-6 medium-4 columns calendar-date equal">
    <div class="valign">
      <div class="valign-cell">
        <div class="month">[start-date format="M"]</div>
        <div class="day">[start-date format="j"]</div>
      </div>
    </div>
  </div>
  <div class="small-18 medium-20 columns calendar-details equal">
    <div class="post-label post-label--calendar not-custom"><i class="far fa-calendar fa-lg" aria-hidden="true"></i> Event</div>
    <div class="title">[title]</div>
    <div class="start-end not-custom">[if-whole-day]All Day[/if-whole-day][if-not-whole-day][start-time] to [end-time][/if-not-whole-day]</div>
    <div class="date custom-content">
        <span class="month">[start-date format="M"]</span>
        <span class="day">[start-date format="j"],</span>
        <span class="day">[start-date format="Y"] |</span>
        <span class="start-end">[if-whole-day]All Day[/if-whole-day][if-not-whole-day][start-time] - [end-time][/if-not-whole-day]</span>
    </div>
    <div class="location">[location]</div>
  </div>
</div>

<div id="[id]" class="reveal reveal--calendar" data-reveal="" data-deep-link="true" data-update-history="true">
  <button class="close-button" type="button" data-close="" aria-label="Close modal">
    <span aria-hidden="true">×</span>
  </button>
  <div class="post-label post-label--calendar"><i class="far fa-calendar fa-lg" aria-hidden="true"></i> Event</div>
  <h3>[title]</h3>
  <div class="mbm">
    [start-date]
    <div class="start-end">[if-whole-day]All Day[/if-whole-day][if-not-whole-day][start-time] to [end-time], [duration][/if-not-whole-day]</div>
    <div class="location">[location]</div>
    [end-location-link]map[/end-location-link]

  </div>
  [description html="yes"]

</div>