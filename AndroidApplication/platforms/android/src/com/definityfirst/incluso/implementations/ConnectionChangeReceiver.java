package com.definityfirst.incluso.implementations;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.widget.Toast;

/**
 * Created by humberto.castaneda on 11/18/2015.
 */
public class ConnectionChangeReceiver extends BroadcastReceiver
{
    @Override
    public void onReceive( Context context, Intent intent )
    {
        Global global=Global.getInstance();

        boolean isConnected=false;
        ConnectivityManager connectivityManager = (ConnectivityManager) context.getSystemService( Context.CONNECTIVITY_SERVICE );
        NetworkInfo activeNetInfo = connectivityManager.getActiveNetworkInfo();
        NetworkInfo mobNetInfo = connectivityManager.getNetworkInfo(     ConnectivityManager.TYPE_MOBILE );
        if ( activeNetInfo != null )
        {
            if (activeNetInfo.isConnected()){
                isConnected=true;
            }
            //Toast.makeText( context, "Active Network Type : " + activeNetInfo.getTypeName(), Toast.LENGTH_SHORT ).show();
        }

        if( mobNetInfo != null )
        {

            if (mobNetInfo.isConnected()){
                isConnected=true;
            }
            //Toast.makeText( context, "Mobile Network Type : " + mobNetInfo.getTypeName(), Toast.LENGTH_SHORT ).show();
        }

            global.getMainActivity().loadUrl("javascript:updateConnectionStatus("+isConnected+")");

    }
}
