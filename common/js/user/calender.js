document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
      locale: 'ko',
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev',
        center: 'title',
        right: 'next'
      },
      dayMaxEventRows: true,
      events: [
        {
          title: '<strong style="color:#333;">📌 면접 안내</strong><br><a href="#">자세히 보기</a>',
          start: '2025-07-10',
          display: 'block'
        },
        {
          title: '<button style="padding:4px 8px;font-size:12px;">제출 마감</button>',
          start: '2025-07-20',
          display: 'block'
        }
      ],
      eventContent: function(arg) {
        return { html: arg.event.title }; // ⬅️ HTML 그대로 출력
      },
      dayCellDidMount: function(info) {
        const day = info.date.getDay();
        if (day === 0 || isHoliday(info.date)) {
          info.el.classList.add('fc-holiday');
        }
      }
    });

    calendar.render();

    // ✅ 간단한 공휴일 체크 함수
    function isHoliday(date) {
      const ymd = date.toISOString().slice(0, 10).replace(/-/g, '');
      const holidays = ['20250717', '20250815']; // 예시: 제헌절, 광복절
      return holidays.includes(ymd);
    }
  });