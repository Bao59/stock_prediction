# 股價趨勢分析與視覺化系統
本專案是一套金融資料趨勢分析網站，整合股票歷史資料 API、Python Flask 模型服務、PHP 前後端串接、MySQL 查詢紀錄與 Chart.js 圖表視覺化。
專案重點不在於宣稱能準確預測投資結果，而是練習將資料取得、模型運算、API 串接、資料保存與網頁視覺化整合成可操作的 Web 系統。

## Demo
http://34.81.229.1/stock_prediction/frontend/index.php

## 使用技術
* Python Flask
* yfinance
* scikit-learn
* PHP
* MySQL
* Chart.js
* GCP Ubuntu
* Nginx
* tmux
* Linux venv

## 核心功能
* 輸入股票代碼並取得歷史收盤價資料
* 使用線性回歸建立股價趨勢線
* 將實際股價與模型趨勢線以 Chart.js 顯示
* 使用 MySQL 儲存查詢紀錄
* 透過 GCP Ubuntu 主機部署網站服務

## 系統流程
使用者輸入股票代碼後，由 PHP 接收前端表單請求，呼叫 Python Flask API。Flask 後端透過 yfinance 取得股票歷史資料，使用 scikit-learn 建立線性回歸趨勢分析，再將結果以 JSON 回傳前端，最後由 Chart.js 繪製實際股價與趨勢線。

## 專案重點
本專案的重點不是預測股價漲跌，而是展示資料串接、模型服務、前後端整合、資料庫紀錄與雲端部署的完整流程。

## 開發與部署經驗
開發過程中處理了 Python venv 環境隔離、tmux 背景執行、Nginx 路徑設定、Port 佔用、Linux 權限與前後端資料串接等問題。

## 未來改進方向
* 加入技術指標，例如 MA、RSI、MACD
* 加入更完整的錯誤處理
* 將 tmux 改為 systemd 服務管理
* 嘗試 ARIMA 或 LSTM 等時間序列模型
* 補上回測機制，避免只看趨勢線
