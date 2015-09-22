package  com.definityfirst.incluso.modules;

import android.content.Context;
import android.os.Environment;
import android.util.Log;

import java.io.BufferedInputStream;
import java.io.InputStream;
import java.net.URL;
import java.net.URLConnection;

public class DownloadFileFromPHP extends DownloadFile {


	public DownloadFileFromPHP(Context context, DownloadFileListener df, String appFolder){
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
            //por ahora vamos a dar un return
            /*if(1==1){
                finishLoad();
                return null;
            }*/
            URL url = new URL(f_url[0]);
            URLConnection conection = url.openConnection();
            conection.connect();

            // this will be useful so that you can show a tipical 0-100%
            // progress bar
            int lenghtOfFile = conection.getContentLength();

            // download the file
            InputStream input = new BufferedInputStream(url.openStream(),
                    8192);

            //if (input.available()>0){
            if (lenghtOfFile>0){
                df.changeSpinnerText("Instalando última versión");
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