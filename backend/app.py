from flask import Flask, request, jsonify
import yfinance as yf
import pandas as pd
from sklearn.linear_model import LinearRegression
import numpy as np

app = Flask(__name__)

@app.route('/api/predict', methods=['POST'])
def predict_stock():
    req_data = request.json
    ticker_input = req_data.get('ticker', '2330').strip().upper()
    period = req_data.get('period', '1y')

    # 智慧判斷：如果只輸入數字，自動補上台股後綴 .TW
    if ticker_input.isdigit():
        ticker_query = f"{ticker_input}.TW"
    else:
        ticker_query = ticker_input

    try:
        stock_data = yf.download(ticker_query, period=period)
        if stock_data.empty:
            return jsonify({"error": f"找不到代碼為 '{ticker_input}' 的股票資料"}), 404

        # 整理歷史收盤價與日期
        dates = stock_data.index.strftime('%Y-%m-%d').tolist()
        actual_prices = np.ravel(stock_data['Close'].values).tolist()

        # 訓練線性回歸模型產生趨勢線
        x_days = np.arange(len(actual_prices)).reshape(-1, 1)
        y_prices = np.array(actual_prices)
        
        model = LinearRegression()
        model.fit(x_days, y_prices)
        
        trendline = np.ravel(model.predict(x_days)).tolist()
        next_day_pred = float(np.ravel(model.predict([[len(actual_prices)]]))[0])

        return jsonify({
            "ticker": ticker_input,
            "query_period": period,
            "dates": dates,
            "actual_prices": [round(p, 2) for p in actual_prices],
            "trendline": [round(p, 2) for p in trendline],
            "predicted_price": round(next_day_pred, 2)
        })

    except Exception as e:
        return jsonify({"error": "後端處理發生錯誤: " + str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)