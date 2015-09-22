package com.definityfirst.incluso.ui;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.view.Window;

import  com.definityfirst.incluso.R;

public class SpinnerDialog {
	// Variables
	private ProgressDialog mSpinner;
	private Context context;

	boolean isVisible = false;

	// Constructor
	public SpinnerDialog(Context context) {
		mSpinner = new ProgressDialog(context);
		mSpinner.requestWindowFeature(Window.FEATURE_NO_TITLE);
		mSpinner.setMessage(context.getString(R.string.general_label_loading));
		mSpinner.setCancelable(false);
		this.context = context;
	}


	// Methods
	public void showDialog(String customMessage) {
		try {
			mSpinner.setMessage(customMessage);
			((Activity) this.context).runOnUiThread(new Runnable() {
				public void run() {
					if(!((Activity)context).isFinishing()){
						mSpinner.show();
						setIsVisible(true);
					}


				}
			});
		} catch (Exception ex) {}
	}
	// Methods
	public void showDialog() {
		try {
			mSpinner.setMessage(context.getString(R.string.general_label_loading));
			((Activity) this.context).runOnUiThread(new Runnable() {
				public void run() {
					if(!((Activity)context).isFinishing()){
						mSpinner.show();
						setIsVisible(true);
					}


				}
			});
		} catch (Exception ex) {}
	}
	
	public void hideDialog() {
		try {
			((Activity) this.context).runOnUiThread(new Runnable() {
				public void run() {
					if(!((Activity)context).isFinishing()){
						mSpinner.dismiss();
						setIsVisible(false);
					}

				}
			});
		} catch (Exception ex) {}
	}

	public void setIsVisible(boolean isVisible) {
		this.isVisible = isVisible;
	}

	public boolean isVisible() {
		return isVisible;
	}

	public void setText(String text){
		mSpinner.setMessage(text);
	}


}
