document.addEventListener('DOMContentLoaded', function () {
  // ログインボタン
  const btn = document.getElementById('clpj_btn_login');
  if( btn){
    btn.addEventListener('click', function(event){
      btn.disabled = true; // 二重送信防止

      const login_name = document.getElementById('clpj_login_name').value;
      const nonce = btn.dataset.nonce;

      fetch( ajaxUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
          action: 'login',
          _wpnonce: nonce,
          clpj_login_name: login_name
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('ログイン成功！');
          location.reload();
        } else {
          if(data.required) {
            const pass = prompt("管理者パスワードを入力してください：");
            if( pass === null){
              alert("キャンセルされました。");
            }else{
              re_login( ajaxUrl, nonce, login_name, pass);
            }
          }
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

    }); // ここまで： ログインボタン

  }
});

function re_login( ajaxUrl, nonce, login_name, pass) {
  const btn = document.getElementById('clpj_btn_login');

  fetch( ajaxUrl, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: new URLSearchParams({
      action: 'login',
      _wpnonce: nonce,
      clpj_login_name: login_name,
      clpj_pass: pass
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('ログイン成功！');
      location.reload();
    } else {
      if(data.required) {
        const pass = prompt("管理者パスワードを入力してください：");
        if( pass === null){
          alert("キャンセルされました。");
        }else{
          re_login( ajaxUrl, nonce, login_name, pass);
        }
      }
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

}
// End of file