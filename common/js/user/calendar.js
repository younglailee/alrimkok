document.addEventListener('DOMContentLoaded', function () {
  var calendarEl = document.getElementById('calendar');

  var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'ko',
      headerToolbar: false,
      dayCellContent: function (arg) {
          return { html: String(arg.date.getDate()) };
      },
      events: events,
      eventClick: function(info) {
          if (info.event.url) {
              window.open(info.event.url, '_blank');
              info.jsEvent.preventDefault(); // anchor 기본 동작 막음
          }
      },
    dayMaxEvents: true, // 날짜당 최대 표시할 이벤트 수 (나머지는 +N 더보기로 표시됨)
    moreLinkContent: function(args) {
      return {
        html: `<span class="fc-more-link">+${args.num} 더보기</span>`
      };
    },
    eventDisplay: 'block', // 이벤트를 block 형태로 표시

    // 팝업처럼 보이게 하는 기본 기능 (기본 true)
    dayMaxEventRows: true,
    moreLinkClick: 'popover', // 클릭 시 팝업 (기본값이 'popover')

    // ✅ title 툴팁 보이게 하기 위한 부분
    eventDidMount: function(info) {
      const titleEl = info.el.querySelector('.fc-event-title');
      const titleText = info.event.title;

      if (titleEl) {
        titleEl.setAttribute('title', titleText);
      } else {
        info.el.setAttribute('title', titleText);
      }
    }
    
  });

  calendar.render();

  // 월 이동 버튼 연동 (선택사항)
  document.querySelector('.prev-month').addEventListener('click', function () {
      calendar.prev();
  });
  document.querySelector('.next-month').addEventListener('click', function () {
      calendar.next();
  });

  // 현재 날짜 기준 타이틀 설정
  function updateTitle() {
      const title = calendar.view.title;
      document.querySelector('.calendar-title').textContent = title;
  }
  calendar.on('datesSet', updateTitle);
  updateTitle();
});