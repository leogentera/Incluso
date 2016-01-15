package com.definityfirst.incluso.implementations;

import java.io.BufferedInputStream;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

import android.content.Context;
import android.os.Environment;
import android.util.Log;

public class DownloadFileFromURL extends DownloadFile {


	public DownloadFileFromURL(Context context, DownloadFileListener df, String appFolder){
       super(context, df, appFolder);
	}
    /**
     * Before starting background thread Show Progress Bar Dialog
     * */
    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        //showDialog(progress_bar_type);
    }

    /**
     * Downloading file in background thread
     * */
    @Override
    protected String doInBackground(String... f_url) {

        String path=Environment
                .getExternalStorageDirectory().toString();
        try {
            URL url = new URL(f_url[0]);
            HttpURLConnection conection = (HttpURLConnection) url.openConnection();
            conection.setRequestMethod("GET");
            conection.setRequestProperty("Content-Type", "application-json");
            conection.setRequestProperty("Accept", "application-json");

            conection.setDoInput(true);
            conection.setDoOutput(true);
            conection.connect();

            // this will be useful so that you can show a tipical 0-100%
            // progress bar
            int lenghtOfFile = conection.getContentLength();

            // download the file
            InputStream input = new BufferedInputStream(url.openStream());

            if (input.available()>0){
                df.changeSpinnerText("Instalando ultima version");
            }
            else{
                finishLoad();
                return null;
            }
            unZipAndSave(input, path);
            input.close();


        } catch (Throwable e) {
            Log.e("Error: ", e.getMessage());
        }
        finishLoad();
        return null;
    }

    /**
     * Updating progress bar
     * */
    protected void onProgressUpdate(String... progress) {
        // setting progress percentage
        //pDialog.setProgress(Integer.parseInt(progress[0]));
    }

    /**
     * After completing background task Dismiss the progress dialog
     * **/
    @Override
    protected void onPostExecute(String file_url) {
        // dismiss the dialog after the file was downloaded
       // dismissDialog(progress_bar_type);

    }

}