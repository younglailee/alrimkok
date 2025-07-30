$(function(){

    //=============================================== 마이페이지 
    $('#mp .tab-group').each(function () {
        const $group = $(this); // 현재 tab-group 컨테이너

        // 선택 항목 추가
        function updateChoiceItem(tabName, value, labelText) {
            const $choiceList = $group.find('.choice');
            const exists = $choiceList.find('li').filter(function () {
                return $(this).text().includes(labelText) && $(this).text().includes(tabName);
            }).length;

            if (!exists) {
                const $item = $(`
                    <li data-tab="${tabName}" data-value="${value}">
                        <span>${tabName}</span> > <span>${labelText}</span>
                        <div class="close">닫기</div>
                    </li>
                `);
                $choiceList.append($item);
            }
        }

        // 선택 항목 제거
        function removeChoiceItem(tabName, value) {
            $group.find('.choice li').filter(function () {
                return $(this).data('tab') === tabName && $(this).data('value') === value;
            }).remove();
        }

        // 전체선택 체크박스 이벤트
        $group.find('.tab-panel .check-all').on('change', function () {
            const isChecked = $(this).is(':checked');
            const $panel = $(this).closest('.tab-panel');
            const tabName = $group.find('.tab > .active').text().trim();

            $panel.find('input[type="checkbox"]').not(this).each(function () {
                $(this).prop('checked', isChecked).trigger('change');
            });

            if (!isChecked) {
                $panel.find('input[type="checkbox"]').not('.check-all').each(function () {
                    const value = $(this).val();
                    removeChoiceItem(tabName, value);
                });
            }
        });

        // 개별 체크박스 변경 시
        $group.find('.tab-panel input[type="checkbox"]').not('.check-all').on('change', function () {
            const $checkbox = $(this);
            const $panel = $checkbox.closest('.tab-panel');
            const tabName = $group.find('.tab > .active').text().trim();
            const value = $checkbox.val();
            const labelText = $checkbox.next('span').text();

            const allChecked =
                $panel.find('input[type="checkbox"]').not('.check-all').length ===
                $panel.find('input[type="checkbox"]:checked').not('.check-all').length;

            $panel.find('.check-all').prop('checked', allChecked);

            if ($checkbox.is(':checked')) {
                updateChoiceItem(tabName, value, labelText);
            } else {
                removeChoiceItem(tabName, value);
            }
        });

        // 닫기 버튼 이벤트
        $group.find('.choice').on('click', '.close', function () {
            const $li = $(this).closest('li');
            const tabName = $li.data('tab');
            const value = $li.data('value');

            // 해당 checkbox 해제
            $group.find('.tab-panel:visible input[type="checkbox"]').each(function () {
                const $checkbox = $(this);
                if ($checkbox.val() === value) {
                    $checkbox.prop('checked', false).trigger('change');
                }
            });

            $li.remove();
        });

        // ✅ 초기 로딩 시 체크된 항목 처리
        $group.find('.tab-panel input[type="checkbox"]:checked').not('.check-all').each(function () {
            const $checkbox = $(this);
            const value = $checkbox.val();
            const labelText = $checkbox.next('span').text();

            // 탭 이름은 현재 활성화된 탭 기준 (초기 탭 기준)
            const tabName = $group.find('.tab > .active').text().trim();

            updateChoiceItem(tabName, value, labelText);
        });
    });


    // =============================================== 모바일 사업공고 리스트
    $(' .sch-wrap').each(function () {
        const $group = $(this);

        // 선택 항목 추가
        function updateChoiceItem(typeName, value, labelText) {
            const $choiceList = $group.find('.choice');
            const exists = $choiceList.find('li').filter(function () {
                return $(this).data('tab') === typeName && $(this).data('value') === value;
            }).length;

            if (!exists) {
                const $item = $(`
                    <li data-tab="${typeName}" data-value="${value}">
                        <span>${typeName}</span> > <span>${labelText}</span>
                        <div class="close">닫기</div>
                    </li>
                `);
                $choiceList.append($item);
            }
        }

        // 선택 항목 제거
        function removeChoiceItem(typeName, value) {
            $group.find('.choice li').filter(function () {
                return $(this).data('tab') === typeName && $(this).data('value') === value;
            }).remove();
        }

        // 체크박스 상태 변경 처리
        $group.find('.sch input[type="checkbox"]').on('change', function () {
            const $checkbox = $(this);
            const $sch = $checkbox.closest('.sch');
            let typeName = $checkbox.data('target');
            if(typeName === '' || !typeName){
                typeName = $sch.find('.sch-type').first().text().trim();
            }
            const value = $checkbox.val();
            const labelText = $checkbox.next('span').text();

            if ($checkbox.is(':checked')) {
                updateChoiceItem(typeName, value, labelText);
            } else {
                removeChoiceItem(typeName, value);
            }
        });

        // 닫기 버튼 클릭 시 체크 해제
        $group.find('.choice').on('click', '.close', function () {
            const $li = $(this).closest('li');
            const value = $li.data('value');
            const typeName = $li.data('tab');

            // 체크 해제
            $group.find('input[type="checkbox"]').each(function () {
                const $checkbox = $(this);
                const $sch = $checkbox.closest('.sch');
                const schType = $sch.find('.sch-type').first().text().trim();
                if ($checkbox.val() === value && schType === typeName) {
                    $checkbox.prop('checked', false).trigger('change');
                }
            });

            $li.remove();
        });

        // ✅ 초기 로딩 시 체크된 항목 추가
        $group.find('input[type="checkbox"]:checked').each(function () {
            const $checkbox = $(this);
            const $sch = $checkbox.closest('.sch');
            const value = $checkbox.val();
            const labelText = $checkbox.next('span').text();
            const typeName = $sch.find('.sch-type').first().text().trim();

            updateChoiceItem(typeName, value, labelText);
        });
    });


});


//=============================================== 마이페이지 가입정보수정 초기화 버튼
document.addEventListener("DOMContentLoaded", function () {
    const resetButtons = document.querySelectorAll(".resetBtn02");

    resetButtons.forEach(button => {
        button.addEventListener("click", function () {
            // 모든 체크박스 해제
            const checkboxes = document.querySelectorAll(".mpForm03 .mp-chk input[type='checkbox']");
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            // 선택된 항목 표시 영역 초기화 (선택 사항)
            const choiceLists = document.querySelectorAll(".choice");
            choiceLists.forEach(list => {
                list.innerHTML = "";
            });
            
        });
    });
});


