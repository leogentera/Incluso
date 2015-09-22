package  com.definityfirst.incluso;

import android.app.Activity;
import android.app.KeyguardManager;
import android.content.Context;
import android.content.Intent;
import android.media.MediaPlayer;
import android.net.Uri;
import android.os.Bundle;
import android.os.Environment;
import android.view.WindowManager;
import android.widget.MediaController;
import android.widget.Toast;
import android.widget.VideoView;

import java.io.File;

public class VideoPlayer extends Activity {
VideoView videoView;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_video_player);

        videoView= (VideoView) findViewById(R.id.videoview);

        final KeyguardManager myKeyManager = (KeyguardManager)getSystemService(Context.KEYGUARD_SERVICE);
        getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);


        Bundle bundle=getIntent().getExtras();

        String directory="";
        String file="";
        boolean isLocal=true;

        try{
            directory= bundle.getString("directory");
            file= bundle.getString("filename");
            isLocal=bundle.getBoolean("isLocal");
        }catch (Throwable e ){

            Toast.makeText(this, "Ocurrio un error al reproducir el video", Toast.LENGTH_LONG).show();
            finish();
        }

        MediaController mediaController = new
                MediaController(this);
        mediaController.setAnchorView(videoView);
        videoView.setMediaController(mediaController);
        //File video= new File(Environment.getExternalStorageDirectory() + "/app/initializr/assets/media",filename);
        if (isLocal){
            File video= new File(directory,file);

            videoView.setVideoURI(Uri.fromFile(video));
        }
        else{
            videoView.setVideoPath(directory);
        }

        videoView.requestFocus();


        videoView.setOnPreparedListener(new
                                                MediaPlayer.OnPreparedListener() {
                                                    @Override
                                                    public void onPrepared(MediaPlayer mp) {
                                                        getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);
                                                        if (!myKeyManager.inKeyguardRestrictedInputMode())
                                                            mp.start();
                                                    }
                                                });

        //videoView.start();

        videoView.setOnCompletionListener(new MediaPlayer.OnCompletionListener() {
            @Override
            public void onCompletion(MediaPlayer mp) {
                getWindow().clearFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);
                VideoPlayer.this.finish();
            }
        });

    }

    @Override
    protected void onStop() {
       videoView.pause();
        super.onStop();
    }

}
