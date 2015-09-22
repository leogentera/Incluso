package  com.definityfirst.incluso.modules;

import android.content.Context;
import android.os.Environment;
import android.util.Log;

import java.io.BufferedInputStream;
import java.io.File;
import java.io.InputStream;

public class DownloadFileFromPackage extends DownloadFile {

    int rawResource;

    public DownloadFileFromPackage(Context context, DownloadFileListener df, String appFolder , int rawResource){
		super(context, df, appFolder);
        this.rawResource= rawResource;
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
        int count;
        String path=Environment
                .getExternalStorageDirectory().toString();
        try {

            final File file = new File(f_url[0]);

            // this will be useful so that you can show a tipical 0-100%
            // progress bar
            long lenghtOfFile = file.length();

            // download the file
            InputStream input = new BufferedInputStream(context.getResources().openRawResource(rawResource),
                    8192);

            unZipAndSave(input, path);
            input.close();
            

        } catch (Exception e) {
            Log.e("Error: ", e.getMessage());
        }
        final File file = new File( appFolder, "index.html");
        /*Uri uri = Uri.fromFile(file);
        df.loadFinish("file:///storage/sdcard0/app/initializr/index.html");*/
        if ( file.exists())
            df.localLoadFinish("file://" + appFolder+"/index.html");
        else
            df.localLoadFinish(errorPage);
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