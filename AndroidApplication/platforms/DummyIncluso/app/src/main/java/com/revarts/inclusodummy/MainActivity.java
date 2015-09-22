package com.revarts.inclusodummy;

import android.content.Intent;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.Iterator;

public class MainActivity extends AppCompatActivity {

    final static int DUMMY_AVATAR = 0;
    final static int DUMMY_RM = 1;

    Button b_avatar;
    Button b_multiples;
    TextView tv_parametros_juego;
    EditText et_avatar;
    EditText et_rm;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        b_avatar = (Button) findViewById(R.id.b_avatar);
        b_multiples = (Button) findViewById(R.id.b_multiples);
        et_avatar = (EditText) findViewById(R.id.et_vavatar);
        et_rm = (EditText) findViewById(R.id.et_rm);
        tv_parametros_juego = (TextView) findViewById(R.id.tv_parametros_juego);

        et_avatar.setText("com.revarts.JuegosIncluso");
        ;
        b_avatar.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = getPackageManager().getLaunchIntentForPackage(et_avatar.getText().toString());
                intent.setFlags(0);
                //intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                intent.putExtra("game_arguments", "{\n" +
                        "    \"userid\": 103,\n" +
                        "    \"alias\": \"Laura\",\n" +
                        "    \"actividad\": \"Avatar\",\n" +
                        "    \"estrellas\": 0,\n" +
                        "    \"pathimagen\": \"blah\",\n" +
                        "    \"genero\": \"\",\n" +
                        "    \"rostro\": \"\",\n" +
                        "    \"color_de_piel\": \"\",\n" +
                        "    \"estilo_cabello\": \"\",\n" +
                        "    \"color_cavello\": \"\",\n" +
                        "    \"traje_color principal\": \"\",\n" +
                        "    \"traje_color secundario\": \"\",\n" +
                        "    \"escudo\": \"\"\n" +
                        "}\n");

                MainActivity.this.startActivityForResult(intent, DUMMY_AVATAR);
            }
        });
        et_rm.setText("com.revarts.JuegosIncluso");


        b_multiples.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = getPackageManager().getLaunchIntentForPackage(et_rm.getText().toString());
                intent.setFlags(0);
                //intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                intent.putExtra("game_arguments", "{\n" +
                        "   \"userid\": 103,\n" +
                        "   \"actividad\": \"Multi\",\n" +
                        "   \"actividad_terminada\": \"1\",\n" +
                        "   \"resultado\": [\n" +
                        "               {\n" +
                        "                  \"subactividad\":\"Musical\",\n" +
                        "                  \"duración\": 5,\n" +
                        "                  \"fecha_inicio\": \"2015-07-15 14:23:12\",\n" +
                        "                  \"fecha_fin\": \"2015-07-15  14:28:12\",\n" +
                        "                  \"puntaje_interno\": \"13243\",\n" +
                        "                  \"nivel_de_reto\": \"2\",\n" +
                        "                  \"preguntas\":[\n" +
                        "                     {\"pregunta\" : \"¿Nivel inteligencia?\", \"respuesta\" : \"2\"},\n" +
                        "                     {\"pregunta\" : \"¿Me fue fácil completar el reto?\" , \"respuesta\" : \"7\"},\n" +
                        "                     {\"pregunta\" : \"¿Disfruté este reto?\" , \"respuesta\" : \"9\"},\n" +
                        "                     {\"pregunta\" : \"¿Se tocar algún instrumento?\", \"respuesta\" : \"6\"},\n" +
                        "                     {\"pregunta\" : \"¿Te gustó la actividad?\", \"respuesta\" : \"Si\"}\n" +
                        "                  ]\n" +
                        "               },\n" +
                        "               {\n" +
                        "                  \"subactividad\":\"Interpersonal\",\n" +
                        "                  \"duración\": 5,\n" +
                        "                  \"fecha_inicio\": \"2015-07-15 14:23:12\",\n" +
                        "                  \"fecha_fin\": \"2015-07-15  14:28:12\",\n" +
                        "                  \"puntaje_interno\": \"15500\",\n" +
                        "                  \"nivel_de_reto\": \"1\",\n" +
                        "                  \"preguntas\":[\n" +
                        "                    {\"pregunta\" : \"¿Nivel inteligencia?\", \"respuesta\" : \"2\"},\n" +
                        "                    {\"pregunta\" : \"¿Me fue fácil completar el reto?\" , \"respuesta\" : \"7\"},\n" +
                        "                    {\"pregunta\" : \"¿Disfruté este reto?\" , \"respuesta\" : \"9\"},\n" +
                        "                    {\"pregunta\" : \"¿Me gusta enseñar lo que sé a otras personas?\", \"respuesta\" : \"8\"},\n" +
                        "                    {\"pregunta\" : \"¿Te gustó la actividad?\", \"respuesta\" : \"Si\"}\n" +
                        "                  ]\n" +
                        "               },\n" +
                        "               {\n" +
                        "                  \"subactividad\":\"Naturalista\",\n" +
                        "                  \"duración\": 5,\n" +
                        "                  \"fecha_inicio\": \"2015-07-15 14:23:12\",\n" +
                        "                  \"fecha_fin\": \"2015-07-15  14:28:12\",\n" +
                        "                  \"puntaje_interno\": \"15500\",\n" +
                        "                  \"nivel_de_reto\": \"1\",\n" +
                        "                  \"preguntas\":[\n" +
                        "                    {\"pregunta\" : \"¿Nivel inteligencia?\", \"respuesta\" : \"2\"},\n" +
                        "                    {\"pregunta\" : \"¿Me fue fácil completar el reto?\" , \"respuesta\" : \"7\"},\n" +
                        "                    {\"pregunta\" : \"¿Disfruté este reto?\" , \"respuesta\" : \"9\"},\n" +
                        "                    {\"pregunta\" : \"¿Me gustraia tener mi propio jardín en el que pueda cultivar mis alimentos?\", \"respuesta\" : \"5\"},\n" +
                        "                    {\"pregunta\" : \"¿Te gustó la actividad?\", \"respuesta\" : \"Si\"}\n" +
                        "                  ]\n" +
                        "               },\n" +
                        "               {\n" +
                        "                  \"subactividad\":\"Intrapersonal\",\n" +
                        "                  \"duración\": 5,\n" +
                        "                  \"fecha_inicio\": \"2015-07-15 14:23:12\",\n" +
                        "                  \"fecha_fin\": \"2015-07-15  14:28:12\",\n" +
                        "                  \"puntaje_interno\": \"15500\",\n" +
                        "                  \"nivel_de_reto\": \"1\",\n" +
                        "                  \"preguntas\":[\n" +
                        "                    {\"pregunta\" : \"¿Nivel inteligencia?\", \"respuesta\" : \"2\"},\n" +
                        "                    {\"pregunta\" : \"¿Me fue fácil completar el reto?\" , \"respuesta\" : \"7\"},\n" +
                        "                    {\"pregunta\" : \"¿Disfruté este reto?\" , \"respuesta\" : \"9\"},\n" +
                        "                    {\"pregunta\" : \"¿Me gusta evaluar las consecuencias antes de tomar una decision?\", \"respuesta\" : \"4\"},\n" +
                        "                    {\"pregunta\" : \"¿Te gustó la actividad?\", \"respuesta\" : \"Si\"}\n" +
                        "                  ]\n" +
                        "               },\n" +
                        "               {\n" +
                        "                  \"subactividad\": \"Corporal\",\n" +
                        "                  \"duración\": 5,\n" +
                        "                  \"fecha_inicio\": \"2015-07-15 14:23:12\",\n" +
                        "                  \"fecha_fin\": \"2015-07-15  14:28:12\",\n" +
                        "                  \"puntaje_interno\": \"15500\",\n" +
                        "                  \"nivel_de_reto\": \"1\",\n" +
                        "                  \"preguntas\":[\n" +
                        "                    {\"pregunta\" : \"¿Nivel inteligencia?\", \"respuesta\" : \"2\"},\n" +
                        "                    {\"pregunta\" : \"¿Me fue fácil completar el reto?\" , \"respuesta\" : \"7\"},\n" +
                        "                    {\"pregunta\" : \"¿Disfruté este reto?\" , \"respuesta\" : \"9\"},\n" +
                        "                    {\"pregunta\" : \"¿Me gusta ser la primera en bailar en las fiestas?\", \"respuesta\" : \"8\"},\n" +
                        "                    {\"pregunta\" : \"¿Te gustó la actividad?\", \"respuesta\" : \"Si\"}\n" +
                        "                  ]\n" +
                        "               },\n" +
                        "               {\n" +
                        "                  \"subactividad\":\"Espacial\",\n" +
                        "                  \"duración\": 5,\n" +
                        "                  \"fecha_inicio\": \"2015-07-15 14:23:12\",\n" +
                        "                  \"fecha_fin\": \"2015-07-15  14:28:12\",\n" +
                        "                  \"puntaje_interno\": \"15500\",\n" +
                        "                  \"nivel_de_reto\": \"1\",\n" +
                        "                  \"preguntas\":[\n" +
                        "                    {\"pregunta\" : \"¿Nivel inteligencia?\", \"respuesta\" : \"2\"},\n" +
                        "                    {\"pregunta\" : \"¿Me fue fácil completar el reto?\" , \"respuesta\" : \"7\"},\n" +
                        "                    {\"pregunta\" : \"¿Disfruté este reto?\" , \"respuesta\" : \"9\"},\n" +
                        "                    {\"pregunta\" : \"¿Cuándo me dirijo a un lugar nuevo, me es fácil ubicarme?\", \"respuesta\" : \"6\"},\n" +
                        "                    {\"pregunta\" : \"¿Te gustó la actividad?\", \"respuesta\" : \"Si\"}\n" +
                        "                  ]\n" +
                        "               },\n" +
                        "               {\n" +
                        "                  \"subactividad\":\"Matemática\",\n" +
                        "                  \"duración\": 5,\n" +
                        "                  \"fecha_inicio\": \"2015-07-15 14:23:12\",\n" +
                        "                  \"fecha_fin\": \"2015-07-15  14:28:12\",\n" +
                        "                  \"puntaje_interno\": \"15500\",\n" +
                        "                  \"nivel_de_reto\": \"1\",\n" +
                        "                  \"preguntas\":[\n" +
                        "                    {\"pregunta\" : \"¿Nivel inteligencia?\", \"respuesta\" : \"2\"},\n" +
                        "                    {\"pregunta\" : \"¿Me fue fácil completar el reto?\" , \"respuesta\" : \"7\"},\n" +
                        "                    {\"pregunta\" : \"¿Disfruté este reto?\" , \"respuesta\" : \"9\"},\n" +
                        "                    {\"pregunta\" : \"¿Me gusta clasificar cosas por colores, tamaños y tener todo en orden?\", \"respuesta\" : \"2\"},\n" +
                        "                    {\"pregunta\" : \"¿Te gustó la actividad?\", \"respuesta\" : \"Si\"}\n" +
                        "                  ]\n" +
                        "               },\n" +
                        "               {\n" +
                        "                  \"subactividad\":\"Lingüística\",\n" +
                        "                  \"duración\": 5,\n" +
                        "                  \"fecha_inicio\": \"2015-07-15 14:23:12\",\n" +
                        "                  \"fecha_fin\": \"2015-07-15  14:28:12\",\n" +
                        "                  \"puntaje_interno\": \"15500\",\n" +
                        "                  \"nivel_de_reto\": \"1\",\n" +
                        "                  \"preguntas\":[\n" +
                        "                    {\"pregunta\" : \"¿Nivel inteligencia?\", \"respuesta\" : \"2\"},\n" +
                        "                    {\"pregunta\" : \"¿Me fue fácil completar el reto?\" , \"respuesta\" : \"7\"},\n" +
                        "                    {\"pregunta\" : \"¿Disfruté este reto?\" , \"respuesta\" : \"9\"},\n" +
                        "                    {\"pregunta\" : \"¿Me gustan los juegos de palabras y los crucigramas?\", \"respuesta\" : \"10\"},\n" +
                        "                    {\"pregunta\" : \"¿Te gustó la actividad?\", \"respuesta\" : \"Si\"}\n" +
                        "                  ]\n" +
                        "               }\n" +
                        "\n" +
                        "\t]\n" +
                        "\n" +
                        "}\n");
               //MainActivity.this.startActivityForResult(intent, DUMMY_RM);
                MainActivity.this.startActivity(intent);
            }
        });

    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (resultCode == RESULT_OK) {
            String result = "";

            Bundle bu_params = data.getExtras();

            String parametros_juego = "";
            try {
                parametros_juego = bu_params.getString("parametros_juego");
            } catch (Throwable e) {
                tv_parametros_juego.setText("No se encontró el parametro \"parametros_juego\"");
                return;
            }

            if (requestCode == DUMMY_AVATAR) {
                JSONObject jsonObject = null;
                try {
                    jsonObject = new JSONObject(parametros_juego);
                } catch (Throwable e) {
                    tv_parametros_juego.setText("El JSON recibido no es un objeto valido. JSON recibido:" + parametros_juego);
                    return;
                }

                try {
                    result = desglosarObjeto(jsonObject);
                } catch (JSONException e) {
                }

            } else {
                JSONArray jsonArray = null;
                try {
                    jsonArray = new JSONArray(parametros_juego);
                } catch (Throwable e) {
                    tv_parametros_juego.setText("El JSON recibido no es un array valido. JSON recibido:" + parametros_juego);
                    return;
                }

                try {
                    result = desglosarArray(jsonArray);
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            }


            tv_parametros_juego.setText(result);

        } else {
            tv_parametros_juego.setText("El juego ha sido cancelado por el usuario");
        }
    }

    public String desglosarObjeto(JSONObject jsonObject) throws JSONException {
        Iterator<?> keys = jsonObject.keys();
        String result = "";

            while (keys.hasNext()) {
                String key = (String) keys.next();
                result += key + ":";

                if (jsonObject.get(key) instanceof Integer) {
                    result += jsonObject.getInt(key);
                    result += "(Integer)";
                } else if (jsonObject.get(key) instanceof Boolean) {
                    result += jsonObject.getBoolean(key);
                    result += "(Boolean)";
                } else if (jsonObject.get(key) instanceof String) {
                    result += jsonObject.getString(key);
                    result += "(String)";

                }else if (jsonObject.get(key) instanceof JSONObject) {
                    result +="{" + desglosarObjeto(jsonObject.getJSONObject(key))+"}";

                    result += "(JSONObject)";
                }else if (jsonObject.get(key) instanceof JSONArray) {
                    result +="[" + desglosarArray(jsonObject.getJSONArray(key))+"]";

                    result += "(JSONArray)";
                }else {
                    result += "(Unknown)";
                }

                result += System.getProperty("line.separator");
                result += System.getProperty("line.separator");
            }
        return result;
    }

    public String desglosarArray(JSONArray jsonArray) throws JSONException {

        String result="";
        for(int i=0; i<jsonArray.length();i++){

            if (jsonArray.get(i) instanceof Integer) {
                result += jsonArray.getInt(i);
                result += "(Integer)";
            } else if (jsonArray.get(i) instanceof Boolean) {
                result += jsonArray.getBoolean(i);
                result += "(Boolean)";
            } else if (jsonArray.get(i) instanceof String) {
                result += jsonArray.getString(i);
                result += "(String)";
            } else if (jsonArray.get(i) instanceof JSONObject) {
                result +="{" + desglosarObjeto(jsonArray.getJSONObject(i))+"}";

                result += "(JSONObject)";
            }else if (jsonArray.get(i) instanceof JSONArray) {
                result +="[" + desglosarArray(jsonArray.getJSONArray(i))+"]";

                result += "(JSONArray)";
            }else {
                result += "(Unknown)";
            }

            result += System.getProperty("line.separator");
            result += System.getProperty("line.separator");

        }

        return result;

    }


}


