document.addEventListener('DOMContentLoaded', function () {
  // 完登ボタン
  document.querySelectorAll('.clpj_btn_completed').forEach(function (btn) {
    btn.addEventListener('click', function () {
      btn.disabled = true; // 二重送信防止

      const parent = btn.parentElement.parentElement;
      const projectName = parent.dataset.project;
      const nonce = btn.dataset.nonce;

      fetch( ajaxUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
          action: 'completed',
          _wpnonce: nonce,
          projectName: projectName
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('送信成功: ' + data.data);
        } else {
          alert('送信失敗: ' + (data.data || '不明なエラー'));
          btn.disabled = false; // 再び有効に
        }
      })
      .catch(error => {
        console.error('通信エラー:', error);
        alert('通信に失敗しました');
        btn.disabled = false; // 再び有効に
      })
      .finally(() => {
      });
    });
  }); // ここまで： 完登ボタン


  // グレード投票ボタン
  document.querySelectorAll('.clpj_evaluate_grade').forEach(function (selectElement) {
    selectElement.addEventListener('change', function(event){
        const selectedValue = event.target.value;
        const parent = event.target.parentElement;
        const btn = parent.querySelector('.clpj_btn_evaluate');

        if (selectedValue == '0') {
          btn.disabled = true;
        } else {
          btn.disabled = false;
        }

    });
  });

  document.querySelectorAll('.clpj_btn_evaluate').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const parent = btn.parentElement;
      const projectName = parent.dataset.project;
      const nonce = btn.dataset.nonce;
      const evaGrade = parent.querySelector('.clpj_evaluate_grade');
      const evaSubgrade = parent.querySelector('.clpj_evaluate_subgrade');
      let grade = parseFloat(evaGrade.value);
      grade = grade + parseFloat(evaSubgrade.value);

      btn.disabled = true; // 二重送信防止
      evaGrade.disabled = true;
      evaSubgrade.disabled = true;

      fetch( ajaxUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
          action: 'evaluate',
          _wpnonce: nonce,
          projectName: projectName,
          evaluateGrade: grade
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('送信成功: ' + data.data);
        } else {
          alert('送信失敗: ' + (data.data || '不明なエラー'));
            btn.disabled = false; // 再び有効に
            evaGrade.disabled = false;
            evaSubgrade.disabled = false;
        }
      })
      .catch(error => {
        console.error('通信エラー:', error);
        alert('通信に失敗しました');
        btn.disabled = false; // 再び有効に
        evaGrade.disabled = false;
        evaSubgrade.disabled = false;
      })
      .finally(() => {
      });
    });
  }); // ここまで： グレード投票ボタン

});
// End of file