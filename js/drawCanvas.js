let holdClick = false;  // クリックホールドフラグ
let startX = 0;  // 開始座標(X)
let startY = 0;  // 開始座標(Y)
let dispScale = 1;  // 拡大縮小スケール
const MAX_SCALE = 3;  // 拡大の最大値
let pointers = new Map();  // ポイント用マップ
let distance = 0;  // 指２本でタッチした時の指間の距離
let centerX = 0;  // 指２本でタッチした時の平均座標(X)
let centerY = 0;  // 指２本でタッチした時の平均座標(Y)

// 各Canvas
const canvasArea = document.getElementById('clpj_canvas-area');
let imageCvs;
let imageCtx;
let drawCvs;
let drawCtx;
let pointerCvs;
let pointerCtx;
let orgWidth;
let orgHeight;

// Undo/Redo
let saveState = false;  // saveフラグ
const undoStack = [];
const redoStack = [];
const MAX_HISTORY = 20;

const img = new Image();
const lineStack = [];


// イベントリスナー登録（DOMContentLoaded）
document.addEventListener('DOMContentLoaded', function () {

    imageCvs = document.getElementById('clpj_imageCanvas');
    drawCvs = document.getElementById('clpj_drawCanvas');
    pointerCvs = document.getElementById('clpj_pointerCanvas');
    imageCtx = imageCvs.getContext("2d", { willReadFrequently: true });
    drawCtx = drawCvs.getContext("2d", { willReadFrequently: true });
    pointerCtx = pointerCvs.getContext("2d", { willReadFrequently: true });

    // 画像Canvasの描画処理
    const selectElement = document.getElementById('clpj_select_wall');
    selectElement.addEventListener('change', function(event){
        const selectedValue = event.target.value;
        dispScale = 1;

        if (selectedValue == '0') {
            document.getElementById('clpj_btn_postFile').disabled = true;
        } else if (selectedValue == '1') {
            img.src = document.getElementById('clpj_tmpImg_0').innerText;
        } else if (selectedValue == '2') {
            img.src = document.getElementById('clpj_tmpImg_1').innerText;
        } else if (selectedValue == '3') {
            img.src = document.getElementById('clpj_tmpImg_2').innerText;
        } else if (selectedValue == '4') {
            img.src = document.getElementById('clpj_tmpImg_3').innerText;
        } else if (selectedValue == '5') {
            img.src = document.getElementById('clpj_tmpImg_4').innerText;
        } else if (selectedValue == '6') {
            img.src = document.getElementById('clpj_tmpImg_5').innerText;
        } else if (selectedValue == '7') {
            img.src = document.getElementById('clpj_tmpImg_6').innerText;
        } else if (selectedValue == '8') {
            img.src = document.getElementById('clpj_tmpImg_7').innerText;
        } else if (selectedValue == '9') {
            img.src = document.getElementById('clpj_tmpImg_8').innerText;
        } else if (selectedValue == '10') {
            img.src = document.getElementById('clpj_tmpImg_9').innerText;
        }

        img.onload = function () {
            const scale = img.height / img.width;

            if( scale < 1){
                imageCvs.width = canvasArea.clientWidth;
                imageCvs.height = canvasArea.clientWidth * scale;
            }else{
                imageCvs.height = canvasArea.clientHeight;
                imageCvs.width = canvasArea.clientHeight / scale;
            }

            drawCvs.width = imageCvs.width;
            drawCvs.height = imageCvs.height;
            pointerCvs.width = imageCvs.width;
            pointerCvs.height = imageCvs.height;
            orgWidth = imageCvs.width;
            orgHeight = imageCvs.height;

            imageCtx = imageCvs.getContext("2d", { willReadFrequently: true });
            drawCtx = drawCvs.getContext("2d", { willReadFrequently: true });
            pointerCtx = pointerCvs.getContext("2d", { willReadFrequently: true });

            imageCtx.drawImage(img, 0, 0, imageCvs.width, imageCvs.height);
            clear_drawCanvas();
            lineStack.length = 0;
            undoStack.length = 0;
            redoStack.length = 0;
        };
    }); // ここまで：画像Canvasの描画処理


    // 太さ変更
    const slider = document.getElementById('clpj_slider');
    slider.addEventListener('change', function(event){
        document.getElementById('clpj_brushSize').innerHTML = this.value;
    });


    // Undo ボタン押下
    const btnUndo = document.getElementById('clpj_btn_undo');
    btnUndo.addEventListener('click', function(event){
        const tmpStack = [];
        const tmp = lineStack.length - undoStack.pop();
        for(let i=0; i < tmp; i++){
            tmpStack.push(lineStack.pop());
        }

        redoStack.push(tmpStack);
        restoreState_drawCanvas();

        document.getElementById('clpj_btn_redo').disabled = false;
        if(undoStack.length <= 0) {
            document.getElementById('clpj_btn_undo').disabled = true;
        }
    });


    // Redo ボタン押下
    const btnRedo = document.getElementById('clpj_btn_redo');
    btnRedo.addEventListener('click', function(event){
        undoStack.push(lineStack.length);

        const tmpStack = redoStack.pop();
        const tmp = tmpStack.length;
        for(let i=0; i < tmp; i++){
            lineStack.push(tmpStack.pop());
        }
        restoreState_drawCanvas();

        document.getElementById('clpj_btn_undo').disabled = false;
        if(redoStack.length <= 0) {
            document.getElementById('clpj_btn_redo').disabled = true;
        }
    });


    // グレード セレクト
    const selectGrade = document.getElementById('clpj_select_grade');
    selectGrade.addEventListener('change', function(event){
        const selectGrade = event.target.value;
        const selectedValue = event.target.value;
        //const selectElement = document.getElementById('clpj_select_wall');

        if (selectGrade == '0') {
            document.getElementById('clpj_btn_postFile').disabled = true;
        }else{
            document.getElementById('clpj_btn_postFile').disabled = false;
        }

/*
        if (selectGrade == '0') {
            document.getElementById('clpj_btn_postFile').disabled = true;
        } else if (selectGrade == '1') {
            document.getElementById('clpj_btn_postFile').disabled = false;
        } else if (selectGrade == '2') {
            document.getElementById('clpj_btn_postFile').disabled = false;
        } else if (selectGrade == '3') {
            document.getElementById('clpj_btn_postFile').disabled = false;
        } else if (selectGrade == '4') {
            document.getElementById('clpj_btn_postFile').disabled = false;
        } else if (selectGrade == '5') {
            document.getElementById('clpj_btn_postFile').disabled = false;
        } else if (selectGrade == '6') {
            document.getElementById('clpj_btn_postFile').disabled = false;
        } else if (selectGrade == '7') {
            document.getElementById('clpj_btn_postFile').disabled = false;
        } else if (selectGrade == '8') {
            document.getElementById('clpj_btn_postFile').disabled = false;
        } else if (selectGrade == '9') {
            document.getElementById('clpj_btn_postFile').disabled = false;
        } else if (selectGrade == '10') {
            document.getElementById('clpj_btn_postFile').disabled = false;
        } else if (selectGrade == '11') {
            document.getElementById('clpj_btn_postFile').disabled = false;
        } else if (selectGrade == '12') {
            document.getElementById('clpj_btn_postFile').disabled = false;
        } else if (selectGrade == '13') {
            document.getElementById('clpj_btn_postFile').disabled = false;
        } else if (selectGrade == '14') {
            document.getElementById('clpj_btn_postFile').disabled = false;
        } else if (selectGrade == '15') {
            document.getElementById('clpj_btn_postFile').disabled = false;
        }
*/
    });

    // 「投稿」ボタン
    const btnPostFile = document.getElementById('clpj_btn_postFile');
    btnPostFile.addEventListener('click', function(event){
        btnPostFile.disabled = true;

        const comment = document.getElementById('clpj_txt_comment').value;
        const grade = document.getElementById('clpj_select_grade').value;
        const subgrade = document.getElementById('clpj_subgrade_slider').value;

        let fileName;
        const selectedValue = document.getElementById('clpj_select_wall').value
        if (selectedValue == '0') {
        } else if (selectedValue == '1') {
            fileName = 'clpj_Wall_0';
        } else if (selectedValue == '2') {
            fileName = 'clpj_Wall_1';
        } else if (selectedValue == '3') {
            fileName = 'clpj_Wall_2';
        } else if (selectedValue == '4') {
            fileName = 'clpj_Wall_3';
        } else if (selectedValue == '5') {
            fileName = 'clpj_Wall_4';
        } else if (selectedValue == '6') {
            fileName = 'clpj_Wall_5';
        } else if (selectedValue == '7') {
            fileName = 'clpj_Wall_6';
        } else if (selectedValue == '8') {
            fileName = 'clpj_Wall_7';
        } else if (selectedValue == '9') {
            fileName = 'clpj_Wall_8';
        } else if (selectedValue == '10') {
            fileName = 'clpj_Wall_9';
        }

        mergeCanvasesToBlob( imageCvs, drawCvs, (blob) => {
            const url_make_project = document.getElementById('clpj_url_make_project').innerText;
            const formData = new FormData();
            formData.append("clpj_project_image", blob, fileName);
            formData.append("clpj_project_comment", comment);
            formData.append("clpj_project_grade", grade);
            formData.append("clpj_project_subgrade", subgrade);

            console.log(url_make_project);

            fetch( url_make_project, {
                method: 'POST',
                body: formData,
            })
            .then(response => response.text())
            .then(data => {
                console.log("アップロード完了:", data);
                alert("アップロード成功！");
            })
            .catch(error => {
                console.error("アップロード失敗:", error);
                alert("アップロードに失敗しました");
            });
        });

        btnPostFile.disabled = false;

    }); // ここまで：「投稿」ボタン

});// ここまで：イベントリスナー登録（DOMContentLoaded）


// イベントリスナー登録（window Loaded）
window.addEventListener('load', function (event) {
    // ポインタCanvasのマウスイベント処理
    // マウスクリックイベント
    pointerCvs.addEventListener('pointerdown', pointerCvs_pointerDown, { passive: false });
    // マウス移動イベント
    pointerCvs.addEventListener('pointermove', pointerCvs_pointerMove, { passive: false });
    // マウスクリック外しイベント
    pointerCvs.addEventListener('pointerup', pointerCvs_pointerUp, { passive: false });
    // マウスホイールイベント(パッシブでないリスナーとして登録)
    pointerCvs.addEventListener('wheel', pointerCvs_mouseWheel, { passive: false });
    // エリアから外れたときのイベント
    pointerCvs.addEventListener('pointerleave', pointerCvs_pointerLeave, { passive: false });

});


// Undo/Redo用 状態保存
function saveState_drawCanvas() {
    while (undoStack.length >= MAX_HISTORY){
        undoStack.shift(); // 古いの捨てる
    }
    undoStack.push(lineStack.length);

    redoStack.length = 0; // redo無効化
    document.getElementById('clpj_btn_redo').disabled = true;
    document.getElementById('clpj_btn_undo').disabled = false;
}

// Undo/Redo用 状態復元
function restoreState_drawCanvas() {
    drawCvs.width = imageCvs.width;
    drawCvs.height = imageCvs.height;
    drawCtx = drawCvs.getContext("2d", { willReadFrequently: true });

    clear_drawCanvas();
    for (const line of lineStack) {
        draw_drawCvs( line.x1 * dispScale, line.y1 * dispScale, line.x2 * dispScale, line.y2 * dispScale, line.brushSize * dispScale);
    }

}



// マウスクリックイベント
function pointerCvs_pointerDown(e) {
    e.preventDefault();
    pointers.set(e.pointerId, { x: e.clientX, y: e.clientY });

    if(pointers.size == 1){
        saveState = true;

        const rect = pointerCvs.getBoundingClientRect()
        holdClick = true;
        startX = e.clientX - rect.left;
        startY = e.clientY - rect.top;

    } else if(pointers.size == 2){
        holdClick = false;

        const [p1, p2] = Array.from(pointers.values());
        distance = getDistance(p1, p2);
        centerX = (p1.x + p2.x) /2;
        centerY = (p1.y + p2.y) /2;
    }

}

// マウス移動イベント
function pointerCvs_pointerMove(e) {
    e.preventDefault();

    if(saveState){
        saveState_drawCanvas();
        saveState = false;
    }

    draw_pointerCvs(e);
    if(pointers.size == 1){
        if (holdClick) {
            erase_drawCvs(e);
        }

    } else if(pointers.size == 2){
        if (pointers.has(e.pointerId)) {
            pointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
        }

        const [p1, p2] = Array.from(pointers.values());
        const new_centerX = (p1.x + p2.x) /2;
        const new_centerY = (p1.y + p2.y) /2;

        if(centerX == 0){ centerX = new_centerX;}
        if(centerY == 0){ centerY = new_centerY;}
        if (distance == 0) {distance = getDistance(p1, p2);}

        distScale = getDistance(p1, p2) / distance;

        if ((distScale < 1.2) && (distScale > 0.8)){
            canvasArea.scrollLeft += (centerX - new_centerX);
            canvasArea.scrollTop += (centerY - new_centerY);

        } else {
            distScale = (distScale - 1)*0.25;
            dispScale = dispScale + distScale;
            if (dispScale < 1) {
                dispScale = 1;
            } else if (dispScale > MAX_SCALE) {
                dispScale = MAX_SCALE;
            }
            // 小数点第二以下切り捨て
            dispScale = Math.round(dispScale * 10) / 10;
            // 算出した拡大率で描画
            zoom( centerX, centerY);            
        }

        centerX = new_centerX;
        centerY = new_centerY;
//        distance = getDistance(p1, p2);
    }
}

// マウスクリック外しイベント
function pointerCvs_pointerUp(e) {
    e.preventDefault();

    if(holdClick){
        holdClick = false;
        erase_drawCvs(e);
    }

    pointers.delete(e.pointerId);
    centerX = 0;
    centerY = 0;
    startX = 0;
    startY = 0;

}

// マウスホイール変更イベント
function pointerCvs_mouseWheel(e) {
    // ページスクロールを無効化
    e.preventDefault();

    const rect = imageCvs.getBoundingClientRect()
    startX = e.clientX - rect.left;
    startY = e.clientY - rect.top;

    // 拡大率算出
    let temp = e.deltaY < 0 ? 1 : -1;
    dispScale += (0.1 * temp);
    // 拡大率は1～5まで
    if (dispScale < 1) {
        dispScale = 1;
    } else if (dispScale > MAX_SCALE) {
        dispScale = MAX_SCALE;
    }
    // 小数点第二以下切り捨て
    dispScale = Math.round(dispScale * 100) / 100;
    // 算出した拡大率で描画
    zoom();
}

// ポインター間の距離を算出
function getDistance(p1, p2) {
    const dx = p1.x - p2.x;
    const dy = p1.y - p2.y;
    return Math.sqrt(dx * dx + dy * dy);
}

// エリアから外れたときのイベント
function pointerCvs_pointerLeave(e) {
    e.preventDefault();

    if(pointers.size == 1){
        // ポインター除去
        pointerCtx.clearRect(0, 0, imageCvs.width, imageCvs.height)
    }

    pointerCvs_pointerUp(e);
}


// 拡大縮小処理
function zoom() {
    const newWidth = orgWidth * dispScale;
    const newHeight = orgHeight * dispScale;

    const canvasIds = [
        'clpj_imageCanvas',
        'clpj_drawCanvas',
        'clpj_pointerCanvas'
    ];

    canvasIds.forEach((id) => {
        const canvas = document.getElementById(id);
        canvas.width = newWidth;
        canvas.height = newHeight;
        canvas.style.width = newWidth; + "px";
        canvas.style.height = newHeight; + "px";
    });
    
    // 再描画（拡大率に応じて拡大して描画）
    imageCtx = imageCvs.getContext("2d", { willReadFrequently: true });
    drawCtx = drawCvs.getContext("2d", { willReadFrequently: true });

    // imageData は元サイズなので、drawImage で拡大表示する
    imageCtx.drawImage(img, 0, 0, newWidth, newHeight);

    clear_drawCanvas();
    for (const line of lineStack) {
        draw_drawCvs( line.x1 * dispScale, line.y1 * dispScale, line.x2 * dispScale, line.y2 * dispScale, line.brushSize * dispScale);
    }

    const Rect = imageCvs.getBoundingClientRect()
    let offsetX = 0;
    let offsetY = 0;
    offsetX = (startX * dispScale) - (orgWidth*0.5);
    offsetY = (startY * dispScale) - (orgHeight*0.5);

    canvasArea.scrollLeft = offsetX;
    canvasArea.scrollTop = offsetY;

    console.error("scale:",dispScale);
    console.error("x:",startX);
    console.error("y:",startY);
    console.error("x:",offsetX);
    console.error("y:",offsetY);

}// ここまで：拡大縮小処理


// pointerCanvasエリア描画
function draw_pointerCvs(e) {
    // 事前のポインタ描画を除去
    pointerCtx.clearRect(0, 0, imageCvs.width, imageCvs.height)

    // 消しゴムなので白固定
    pointerCtx.strokeStyle = 'rgba(255, 255, 255, 1)';

    pointerCtx.lineWidth = document.getElementById('clpj_brushSize').innerHTML; // 太さ
    pointerCtx.lineCap = 'round'; // 円

    const rect = pointerCvs.getBoundingClientRect()
    offsetX = e.clientX - rect.left;
    offsetY = e.clientY - rect.top;

    pointerCtx.beginPath();
    pointerCtx.moveTo(offsetX, offsetY);
    pointerCtx.lineTo(offsetX, offsetY); // 開始座標と終了座標を同じ
    pointerCtx.stroke(); // 描画
    pointerCtx.closePath();
}

// drawCanvasエリア描画(消しゴム)
function erase_drawCvs(e) {
    const rect = pointerCvs.getBoundingClientRect()
    offsetX = e.clientX - rect.left;
    offsetY = e.clientY - rect.top;

    draw_drawCvs( startX, startY, offsetX, offsetY, document.getElementById('clpj_brushSize').innerHTML);

    lineStack.push({ x1: startX/dispScale, x2: offsetX/dispScale, y1: startY/dispScale, y2: offsetY/dispScale, brushSize: drawCtx.lineWidth/dispScale});

    // 次の描画に向けて現在の座標を保持（開始座標・終了座標を同じ座標にしてしまうと、マウスを高速に移動したときに歯抜け状態になる）
    startX = offsetX;
    startY = offsetY;
}

function draw_drawCvs( startX, startY, offsetX, offsetY, brushSize){
    drawCtx.lineWidth = brushSize;
    drawCtx.lineCap = 'round'; // 先端の形状
    drawCtx.strokeStyle = 'rgba(0, 0, 0, 1)'; // 色はなんでもよいが、透過度は1にする
    drawCtx.globalCompositeOperation = 'destination-out' // 塗りつぶした個所を透明化
    drawCtx.beginPath();
    drawCtx.moveTo(startX, startY); // 開始座標（前回座標）
    drawCtx.lineTo(offsetX, offsetY); // 終了座標（現在座標）
    drawCtx.stroke(); // 描画
    drawCtx.closePath();
}

// drawCanvasをクリア
function clear_drawCanvas() {
    drawCtx.clearRect(0, 0, drawCvs.width, drawCvs.height);

    drawCtx.beginPath();
    drawCtx.fillStyle = 'rgba( 0, 0, 0, 0.7)';
    drawCtx.fillRect(0, 0, drawCvs.width, drawCvs.height);
}


// Canvasの合成
function mergeCanvasesToBlob( cvs1, cvs2, callback) {
    const mergedCvs = document.createElement("canvas");
    mergedCvs.width = cvs1.width;
    mergedCvs.height = cvs1.height;
    const mergedCtx = mergedCvs.getContext("2d");

    // 背景画像を描画（下層）
    mergedCtx.drawImage(cvs1, 0, 0);
    // 手書きレイヤーを描画（上層）
    mergedCtx.drawImage(cvs2, 0, 0);

    mergedCvs.toBlob((blob) => {
        callback(blob);
    }, "image/jpeg");

    return mergedCtx;
}

// End of file