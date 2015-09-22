package  com.definityfirst.incluso;

import org.apache.cordova.CallbackContext;
import org.apache.cordova.CordovaPlugin;
import org.json.JSONArray;
import org.json.JSONException;

import android.content.Context;
import android.widget.Toast;

public class MyClass extends CordovaPlugin {
	 public static final String ACTION_ADD_CALENDAR_ENTRY = "addCalendarEntry"; 
	 Context context;
	 @Override
	 public boolean execute(String action, JSONArray args, CallbackContext callbackContext) throws JSONException {
		 try {
			    if (ACTION_ADD_CALENDAR_ENTRY.equals(action)) { 
			             Toast.makeText(context, "Hola Mundo", Toast.LENGTH_LONG);
			       callbackContext.success();
			       return true;
			    }
			    callbackContext.error("Invalid action");
			    return false;
			} catch(Exception e) {
			    System.err.println("Exception: " + e.getMessage());
			    callbackContext.error(e.getMessage());
			    return false;
			}
	  
	 }
}